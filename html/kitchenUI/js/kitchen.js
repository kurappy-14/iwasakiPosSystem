const refreshInterval = 1000; // 1秒ごとに更新

// 注文データを取得してUIを更新
function fetchOrders() {
    fetch('api/get_orders.php')
        .then(response => response.json())
        .then(data => {
            const statusContainers = {
                2: document.getElementById('preparing-orders'),
                3: document.getElementById('cooking-orders'),
                4: document.getElementById('pickup-orders'),
            };

            Object.values(statusContainers).forEach(container => container.innerHTML = '');

            data.forEach(order => {
                const orderElement = createOrderElement(order);
                const container = statusContainers[order.provide_status];
                if (container) container.appendChild(orderElement);
            });
        });
}

// 注文要素を作成
function createOrderElement(order) {
    const orderElement = document.createElement('div');
    orderElement.classList.add('order');
    orderElement.innerHTML = `
        <div class="order-header">
            <div class="order-info">
                <b>注文ID: ${order.order_id}</b>
                <b>呼び出し番号: ${order.call_number}</b>
            </div>
            <button onclick="updateStatus(${order.order_id}, ${order.provide_status}, ${order.call_number})">次へ</button>
            ${getRevertButton(order.order_id, order.provide_status, order.call_number)}
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
    return orderElement;
}

// ステータスを更新する
async function updateStatus(orderId, currentStatus, callNumber) {
    const nextStatus = getNextStatus(currentStatus);
    if (!nextStatus || (currentStatus === 4 && !confirm('提供完了にしますか？'))) return;

    // callNumberの記述はいらないが、一応範囲が9999までなので書いている
    if (nextStatus === 4 && callNumber < 10000) {
        addNumber(callNumber);
    }

    fetch(`api/update_status.php?order_id=${orderId}&status=${nextStatus}`)
        .then(fetchOrders);
}

// 次のステータスを取得
function getNextStatus(currentStatus) {
    switch (currentStatus) {
        case 1: return 2;
        case 2: return 3;
        case 3: return 4;
        case 4: return 5;
        // currentStatusがなにかしらのエラーでなかった時のために一応おいてあるっぽい
        default: console.log("currentStatusがnullです"); return null;
    }
}

// 呼び出し待ちのキュー？
const waitingNumbers = [];
// 音声再生中かどうか
let isPlaying = false;
// ?
let callWaitTimeoutId = null;

function addNumber(callNumber) {
    // キューに追加したいcallNumberが存在しない場合のみ追加
    if (!waitingNumbers.includes(callNumber))
        waitingNumbers.push(callNumber);

    // 音声再生中でなければ、2秒後に再生処理を実行
    if (!isPlaying) {
        // 既にタイムアウトが設定されている場合はキャンセル
        if (callWaitTimeoutId !== null) {
            clearTimeout(callWaitTimeoutId);
        }
        // 2秒後に再生処理を実行,その後タイムアウトIDをnullにする
        // setTimeoutは非同期処理なので、他に影響を及ぼさないぞ！！
        callWaitTimeoutId = setTimeout(() => {
            callWaitTimeoutId = null;
            processVoiceQueue();
        }, 2000);
    }
}

function removeNumber(callNumber) {
    // 消したい番号がキューにある場合、その番号を削除
    const index = waitingNumbers.indexOf(callNumber);
    if (index >= 0) waitingNumbers.splice(index, 1);
    // 再生中でなければ、2秒後に再生処理を実行
    if (!isPlaying) {
        if (callWaitTimeoutId !== null) {
            clearTimeout(callWaitTimeoutId);
        }
        callWaitTimeoutId = setTimeout(() => {
            callWaitTimeoutId = null;
            startVoiceSession();
        }, 2000);
    } else {
        // 作業中につきコメントアウト
        // 再生中の場合、再生中の番号を削除
        // deleteNumberFromPlayingQueue(callNumber);
    }
}

// 呼び出し番号の音声再生キューを処理
async function startVoiceSession() {
    isPlaying = true;
    
    //新たな番号が追加されなくなるまで繰り返す
    while (waitingNumbers.length) {
        await playVoiceFiles();
        await new Promise(resolve => setTimeout(resolve, 2000));
    }
    isPlaying = false;
}

// 呼び出し番号の音声ファイルを再生
async function playVoiceFiles() {

    await playAudio('./zundamon/お呼び出しします.mp3');
    await playAudio('./zundamon/注文番号.mp3');

    while (waitingNumbers.length) {
        waitingNumbers.sort();
        const number = waitingNumbers.shift();
        for (const file of generateVoiceFiles(number)) {
            await playAudio(file);
        }
    }

    await playAudio('./zundamon/のお客様.mp3');
    await playAudio('./zundamon/お料理が完成いたしました.mp3');
}

// 呼び出し番号を音声ファイルに変換
function generateVoiceFiles(number) {
    const files = [];
    const units = [1000, 100, 10, 1];
    for (const unit of units) {
        const value = Math.floor(number / unit) * unit;
        if (value > 0) {
            // デバッグ用
            // console.log(`zundamon/${value}${number % unit == 0 || value < 10 ? '番' : ''}.mp3`);
            files.push(`zundamon/${value}${number % unit == 0 || value < 10 ? '番' : ''}.mp3`);
            number %= unit;
        }
    }
    return files;
}

// 音声再生
function playAudio(src) {
    return new Promise((resolve, reject) => {
        const audio = new Audio(src);
        audio.onended = resolve;
        audio.onerror = reject;
        audio.play();
    });
}

// これはリストから完全に消すので関数名などをdeleteにしています
const deleteNumberListFromPlayingQueue = [];

function deleteNumberFromQueue(number) {
    deleteNumberListFromPlayingQueue.push(...generateVoiceFiles(number));
    for (const number of deleteNumberListFromPlayingQueue) {

    }
}


// 戻すボタンの生成
function getRevertButton(orderId, currentStatus, callNumber) {
    const previousStatus = currentStatus === 3 ? 2 : currentStatus === 4 ? 3 : null;
    return previousStatus ? `<button onclick="revertStatus(${orderId}, ${previousStatus}, ${callNumber})">戻す</button>` : '';
}

// ステータスを戻す
function revertStatus(orderId, previousStatus, callNumber) {

    if (previousStatus === 3 && callNumber < 10000){
        removeNumber(callNumber);
    }

    fetch(`api/update_status.php?order_id=${orderId}&status=${previousStatus}`)
        .then(fetchOrders);
}

// ページ読み込み時の処理
window.onload = () => {
    fetchOrders();
    setInterval(fetchOrders, refreshInterval);
};
