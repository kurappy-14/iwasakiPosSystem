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
                        <button onclick="updateStatus(${order.order_id}, ${order.provide_status},${order.call_number})">次へ</button>
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
                
                // デバッグのため、一度非表示にします by hatomato
                // console.log(order.provide_status);
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

// 音声再生用のキュー
const voicequeue = [];
const tempvoicequeue = [];
let voiceWaitingFlag = false; // 音声再生待ちフラグ
let queueDeletedFlag = false; // キュー削除フラグ

// ステータスを更新する
async function updateStatus(orderId, currentStatus,call_number) {
    let nextStatus;

    // 現在のステータスに応じて次のステータスを決定
    if (currentStatus === 1) {
        nextStatus = 2; // 「決済待ち」→「準備中」
    } else if (currentStatus === 2) {
        nextStatus = 3; // 「準備中」→「調理中」
    } else if (currentStatus === 3) {
        nextStatus = 4; // 「調理中」→「提供待ち」
        
        // 提供待ちになったらその番号の音声を再生
        if (call_number < 10000) { // 今は9999番までなら再生
            voicequeue.push(call_number);
            setTimeout(() => {
                if (voiceWaitingFlag == false) { // フラグが立っていない場合のみ再生
                    voiceWaitingFlag = true; // ここでフラグを立てる
                    console.log(`通常再生開始`);
                    playZundamonVoice(voicequeue, tempvoicequeue);
                } else { // フラグが立っている場合はQueueに追加
                    console.log(`再生中なので別のQueueに追加します:${call_number}`);
                    tempvoicequeue.push(call_number);
                }
            }, 5000); // 5秒待ってから音声再生
        } else {
            return;
        }
    
    } else if (currentStatus === 4) {
        if (!confirm('提供完了にしますか？')) {
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
        });
}

async function playZundamonVoice(voicequeue, tempvoicequeue) {
    const audiolist = [];
    audiolist.push(`./zundamon/お呼び出しします.mp3`);
    audiolist.push(`./zundamon/注文番号.mp3`);
    for (let i = 0; i < voicequeue.length; i++) {
        await makeAudioList(voicequeue, audiolist, i);
    }
    audiolist.push(`./zundamon/のお客様.mp3`);
    audiolist.push(`./zundamon/お料理が完成いたしました.mp3`);


    for (let i = 0; i < audiolist.length; i++) {
        const audio = new Audio(audiolist[i]);
        console.log(`再生中: ${audiolist[i]}`); // ログを追加

        try {
            await playAudio(audio);
            // console.log(`再生成功: ${audiolist[i]}`);
            voicequeue.shift();
        } catch (error) {
            console.error(`再生失敗: ${audiolist[i]}`, error);
        }
    }

    console.log(`(」・ω・)」ずん!(/・ω・)/だー!`);

    await moveVoiceInTempQueue(tempvoicequeue, voicequeue);
}

async function moveVoiceInTempQueue(tempvoicequeue, voicequeue) {
    // 再生待ちのQueueがある場合はtempvoicequeueをvoicequeueに移し替えて再生
    // playZundamonVoiceの再帰呼び出し
    if(tempvoicequeue.length > 0) {
        while(tempvoicequeue.length > 0) {
            voicequeue.push(tempvoicequeue[0]);
            tempvoicequeue.shift();
        }
        console.log(voicequeue);
        console.log(tempvoicequeue);
        playZundamonVoice(voicequeue, tempvoicequeue);
    }else if(tempvoicequeue.length == 0) {
        voiceWaitingFlag = false; // 再生が終わったらフラグを戻す
    }else {
        console.log(`これは出ないはずのログだよ！`);
        return;
    }
} 

function playAudio(audio) {
    return new Promise((resolve, reject) => {
        audio.play().then(() => {
            audio.onended = resolve;
        }).catch(reject);
    });
}

function makeAudioList(voicequeue, audiolist,i) {
    // voicequeueをコピーそれを割っていく
    // voicequeueはそのまま残す
    voicequeueCopy = voicequeue[i];
    console.log(`Added to makeAudioList: ${voicequeueCopy}`);
    // 1000以上の場合は`1000の位`をとる
    while(voicequeueCopy != 0) {
        if (voicequeueCopy > 1000 && voicequeueCopy % 1000 != 0) {
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/1000)*1000}.mp3`);
            voicequeueCopy = voicequeueCopy % 1000;
        }
        // 1000の倍数の場合は`1000の位"番"`をとる
        if(voicequeueCopy % 1000 == 0) {
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/1000)*1000}番.mp3`);
            voicequeueCopy = voicequeueCopy % 1000;
            break;
        }
        // 1000未満100以上の場合は`100の位`をとる
        if(voicequeueCopy > 100 && voicequeueCopy % 100 != 0){
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/100)*100}.mp3`);
            voicequeueCopy = voicequeueCopy % 100;
        }
        // 100の倍数の場合は`100の位"番"`をとる
        if(voicequeueCopy % 100 == 0) {
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/100)*100}番.mp3`);
            voicequeueCopy = voicequeueCopy % 100;
            break;
        }
        // 100未満10以上の場合は`10の位`をとる
        if(voicequeueCopy > 10 && voicequeueCopy % 10 != 0){
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/10)*10}.mp3`)
            voicequeueCopy = voicequeueCopy % 10;
        }
        // 10の倍数の場合は`10の位"番"`をとる
        if(voicequeueCopy % 10 == 0) {
            audiolist.push(`zundamon/${Math.floor(voicequeueCopy/10)*10}番.mp3`);
            voicequeueCopy = voicequeueCopy % 10;
            break;
        }
        // 10未満の場合は`1の位"番"`をとる
        if (voicequeueCopy > 0) {
            audiolist.push(`zundamon/${voicequeueCopy}番.mp3`);
            voicequeueCopy = 0;
            break;
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
    if (voicequeue.includes(orderId)) {
        voicequeue.splice(voicequeue.indexOf(orderId), 1);
    }

    fetch(`api/update_status.php?order_id=${orderId}&status=${previousStatus}`)
        .then(() => fetchOrders()); // ステータス更新後に再取得
}

// ページ読み込み時にデータを取得し、定期的に更新
window.onload = () => {
    fetchOrders();
    setInterval(fetchOrders, refreshInterval);
};
