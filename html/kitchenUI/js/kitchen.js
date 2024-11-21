// 一定時間ごとにデータを更新する
const refreshInterval = 1000; // 1秒ごとに更新

function fetchOrders() {
    fetch('api/get_orders.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('preparing-orders').innerHTML = '';
            document.getElementById('cooking-orders').innerHTML = '';
            document.getElementById('pickup-orders').innerHTML = '';
            
            data.forEach(order => {
                
                const orderElement = document.createElement('div');
                orderElement.classList.add('order');
                orderElement.innerHTML = `
                    <div class="order-header">
                        <div class="order-info">
                            <b>注文ID: ${order.order_id}</b>
                            <b>呼び出し番号: ${order.call_number}</b>
                        </div>
                        <button onclick="updateStatus(${order.order_id}, ${order.provide_status})">次へ</button>
                        ${getRevertButton(order.provide_status, order.order_id)}
                    </div>
                    <div class="order-body">
                        ${order.details.map(item => `
                            <div class="order-item">
                                <span>商品名: ${item.product_name}</span>
                                <span>個数: ${item.quantity}</span>
                            </div>
                        `).join('')}
                    </div>
                `;
                        
                // ステータスごとに適切なエリアに表示
                console.log(order.provide_status);
                if (order.provide_status === 2) {
                    document.getElementById('preparing-orders').appendChild(orderElement);
                } else if (order.provide_status === 3) {
                    document.getElementById('cooking-orders').appendChild(orderElement);
                } else if (order.provide_status === 4) {
                    document.getElementById('pickup-orders').appendChild(orderElement);
                }
            });
        });
}




// ステータスを更新する
function updateStatus(orderId, currentStatus) {
    let nextStatus;

    // 現在のステータスに応じて次のステータスを決定
    if (currentStatus === 1) {
        nextStatus = 2; // 「決済待ち」→「準備中」
    } else if (currentStatus === 2) {
        nextStatus = 3; // 「準備中」→「調理中」
    } else if (currentStatus === 3) {
        nextStatus = 4; // 「調理中」→「提供待ち」
    } else if (currentStatus === 4) {
        if(!confirm('提供完了にしますか？')) {
            return; // キャンセルの場合は何もしない
        }
        nextStatus = 5; // 「提供待ち」→「提供完了」
    } else {
        return; // それ以上進めない（既に「提供完了」の場合）
    }

    // ステータスを更新
    fetch(`api/update_status.php?order_id=${orderId}&status=${nextStatus}`)
        .then(() => {
            fetchOrders(); // ステータス更新後に再取得

            // 提供完了になったらその番号の音声を再生
            if (nextStatus === 5 && orderId < 11) { // 今は10番までなら再生
                const audiolist = [`../zundamon/お呼び出しします.mp3`, `../zundamon/${orderId}番.mp3`, `../zundamon/のお客様.mp3`, `../zundamon/お料理が完成いたしました.mp3`];
                playZundamonVoice(audiolist);
            }
        });
        // 確実に`orderID`と`番`の音声は分けるべきだと思う
}

function playZundamonVoice(audiolist) {

    for (let i = 0; i < audiolist.length; i++) {
        const audio = new Audio(audiolist[i]);
        audio.play();
        audio.onended = () => {
            count++;
            if (count === audiolist.length) {
                return;
            }
        }
    }
}

// 前のステータスに戻すボタンを作成
function getRevertButton(currentStatus, orderId) {
    let revertButton = '';

    if (currentStatus === 3) { // 「調理中」の場合
        revertButton = `<button onclick="revertStatus(${orderId}, 2)">戻す</button>`;
    } else if (currentStatus === 4) { // 「提供待ち」の場合
        revertButton = `<button onclick="revertStatus(${orderId}, 3)">戻す</button>`;
    }

    return revertButton;
}

// 前のステータスに戻す
function revertStatus(orderId, previousStatus) {
    fetch(`api/update_status.php?order_id=${orderId}&status=${previousStatus}`)
        .then(() => fetchOrders()); // ステータス更新後に再取得
}

// ページ読み込み時にデータを取得し、定期的に更新
window.onload = () => {
    fetchOrders();
    setInterval(fetchOrders, refreshInterval);
};
