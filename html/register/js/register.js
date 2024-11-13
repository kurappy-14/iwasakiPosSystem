//宣言部(配列の見直し)
let MENU = [];
let FOOD = [{name:"石破首相",price:100,stock:30},{name:"岸田首相",price:200,stock:10},{name:"安倍首相",price:500,stock:100}];
let DRINK = [{name:"カルピスウォーター",price:100,stock:30},{name:"石破首相",price:100,stock:30}];
let SET = [{name:"石破首相",price:100,stock:30}];
let TOPPING = [{name:"石破首相",price:100,stock:30}];
let quantity = [];  //数を記憶する配列
let method = [];    //支払方法を格納
let total = 0;  //合計金額
let orderid; //オーダーID
let referenceid; //一意なID
let callid = 0; //呼び出し番号
let paytype;

let pressbutton;
let interval;

//フラグ宣言部
let enablecount = true; //支払方法選択が出たときに数を固定
let checkoutflag = true;
let printer;    //プリンターが利用可能かどうか

setproduct();
function setproduct(){
    //DBからメニューを取ってくる
    //メニューを作成(タイトルとメニューの見直し)
    MENU = [...FOOD,...DRINK,...SET,...TOPPING];    //配列を結合
    let menudiv = document.getElementById("menu");  //menudivを指定
    let operationdiv;
    for(let i=0;i<MENU.length;i++){ 
        let title,div;
        quantity.push(0);
        //タイトル追加処理
        if (i === 0 && 0 < FOOD.length) {
            title = document.createElement("h2");
            title.textContent = "FOOD";
            menudiv.appendChild(title);
            div = document.createElement("div");
            div.id = "food";
            div.classList.add("grid");
            menudiv.appendChild(div);
            operationdiv = document.getElementById("food");
        }
        else if (i === FOOD.length && 0 < DRINK.length) {
            title = document.createElement("h2");
            title.textContent = "DRINK";
            menudiv.appendChild(title);
            div = document.createElement("div");
            div.id = "drink";
            div.classList.add("grid");
            menudiv.appendChild(div);
            operationdiv = document.getElementById("drink");
        }
        else if (i === (FOOD.length + DRINK.length) && 0 < SET.length) {
            title = document.createElement("h2");
            title.textContent = "SET";
            menudiv.appendChild(title);
            div = document.createElement("div");
            div.id = "setmenu";
            div.classList.add("grid");
            menudiv.appendChild(div);
            operationdiv = document.getElementById("setmenu");
        }
        else if (i === (FOOD.length + DRINK.length + SET.length) && 0 < TOPPING.length) {
            title = document.createElement("h2");
            title.textContent = "TOPPING";
            menudiv.appendChild(title);
            div = document.createElement("div");
            div.id = "topping";
            div.classList.add("grid");
            menudiv.appendChild(div);
            operationdiv = document.getElementById("topping");
        }
        //boxを作成
        let box = document.createElement("div");
        box.classList.add("box");
        //boxitem1,2を作成
        let boxitem1 = document.createElement("div");
        boxitem1.classList.add("boxitem1");
        let boxitem2 = document.createElement("div");
        boxitem2.classList.add("boxitem2");
        //boxitem1の中の要素を作成
        let productname = document.createElement("p");
        productname.classList.add("productname");
        productname.textContent = MENU[i].name;

        let productprice = document.createElement("p");
        productprice.classList.add("price");
        productprice.textContent = "¥"+MENU[i].price;

        let productstock = document.createElement("p");
        productstock.classList.add("stock");
        productstock.textContent = "在庫:"+MENU[i].stock+"個";
        //要素をboxitem1に挿入
        boxitem1.appendChild(productname);
        boxitem1.appendChild(productprice);
        boxitem1.appendChild(productstock);
        //boxitem2の中の要素を作成
        let plus = document.createElement("button");
        plus.textContent = "+";
        plus.id = i;
        plus.classList.add("plus");
        plus.addEventListener("click",function(event){
            increase(event.target.id);
        });
        plus.addEventListener("mousedown",(event)=>{
            pressbutton = setTimeout(()=>{
                interval = setInterval(()=>{
                    if(enablecount){
                        if(quantity[event.target.id]<MENU[event.target.id].stock){
                            quantity[event.target.id]++;
                        }
                        rewrite();
                    }
                },100);
            },1000);
        });
        plus.addEventListener("mouseup",()=>{
            clearTimeout(pressbutton);
            clearInterval(interval);
        });
        plus.addEventListener("mouseleave",()=>{
            clearTimeout(pressbutton);
            clearInterval(interval);
        });

        let count = document.createElement("span");
        count.id = "count"+i;
        count.textContent = "0";
        
        let minus = document.createElement("button");
        minus.textContent = "-";
        minus.id = i;
        minus.classList.add("minus");
        minus.addEventListener("click",function(event){
            decrease(event.target.id);
        });
        minus.addEventListener("mousedown",(event)=>{
            pressbutton = setTimeout(()=>{
                interval = setInterval(()=>{
                    if(enablecount){
                        if(0<quantity[event.target.id]){
                            quantity[event.target.id]--;
                        }
                        rewrite();
                    }
                },100);
            },1000);
        });
        minus.addEventListener("mouseup",()=>{
            clearTimeout(pressbutton);
            clearInterval(interval);
        });
        minus.addEventListener("mouseleave",()=>{
            clearTimeout(pressbutton);
            clearInterval(interval);
        });
        //要素をboxitem2に挿入
        boxitem2.appendChild(plus);
        boxitem2.appendChild(count);
        boxitem2.appendChild(minus);
        //boxitem1,2をboxに挿入
        box.appendChild(boxitem1);
        box.appendChild(boxitem2);
        //boxをdivに挿入
        operationdiv.appendChild(box);
    }
    let header = document.getElementById("header2");
    //合計と会計ボタンを作成
    let totaltext = document.createElement("p");
    totaltext.id = "total";
    totaltext.textContent = "合計:¥0"
    let checkoutbutton = document.createElement("button");
    checkoutbutton.id = "checkout";
    checkoutbutton.textContent = "会計する";
    //会計ボタンを押したときの処理
    checkoutbutton.addEventListener("click", function(event) {
        document.getElementById("payment").classList.remove("hidden");
        enablecount = false;
    });
    //menudivに合計と会計ボタンを追加
    header.appendChild(totaltext);
    header.appendChild(checkoutbutton);
}

//referenceidを発行する
function randomstring(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

//プラスボタンを押したときの処理
function increase(i){
    if(enablecount){
        if(quantity[i]<MENU[i].stock){
            quantity[i]++;
        }
        rewrite();
    }
}
//マイナスボタンを押したときの処理
function decrease(i){
    if(enablecount){
        if(0<quantity[i]){
            quantity[i]--;
        }
        rewrite();
    }
}
//テキストと合計金額を更新する
function rewrite(){
    for(let i=0;i<MENU.length;i++){
        document.getElementById("count"+i).textContent = quantity[i];
    }
    total = 0;
    for(let i=0;i<MENU.length;i++){
        total += MENU[i].price*quantity[i];
    }
    document.getElementById("total").textContent = "合計:¥"+total;
}

//モーダルウィンドウの閉じるボタンが押されたときの処理
document.getElementById("close").onclick = function(){
    document.getElementById("payment").classList.add("hidden");
    enablecount = true;
}

//支払受付中に戻るボタンを押したときの処理
document.getElementById("payreturn").onclick = function(){
    if (confirm("支払い方法選択へ戻りますか？")) {
        document.getElementById("paywait").classList.add("hidden");
        document.getElementById("payment").classList.remove("hidden");
        fetch('php/cancel.php', {
            method: 'POST',
            headers:{
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                referenceid: referenceid
            })
        })
        .then(response => response.json())
        .then(data => {
            connect(-1);
        })
        .catch(error => console.error('Error:', error));
    }
}

//支払方法のクリック処理
document.getElementById("cash").addEventListener("click", function() {
    //現金を選択した時の処理
    document.getElementById("waittitle").textContent = "現金";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "現金";
    //referenceid = randomstring(13);一意IDの作成
});

document.getElementById("JCB").addEventListener("click", function() {
    //JCB/DinersClub/Discoverを選択した時の処理
    document.getElementById("waittitle").textContent = "JCB/Diners Club/Discover";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "クレジットカード";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'CARD_PRESENT'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("Visa").addEventListener("click", function() {
    //Visa/Mastercard/Amexを選択した時の処理
    document.getElementById("waittitle").textContent = "Visa/Mastercard/Amex";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "クレジットカード";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'CARD_PRESENT'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("QUICPay").addEventListener("click", function() {
    //QUICPayを選択した時の処理
    document.getElementById("waittitle").textContent = "QUICPay";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "QUICPay";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'FELICA_QUICPAY'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("iD").addEventListener("click", function() {
    //iDを選択した時の処理
    document.getElementById("waittitle").textContent = "iD";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "iD";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'FELICA_ID'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("IC").addEventListener("click", function() {
    //交通系ICを選択した時の処理
    document.getElementById("waittitle").textContent = "交通系IC";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "交通系IC";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'FELICA_TRANSPORTATION_GROUP'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("PayPay").addEventListener("click", function() {
    //PayPayを選択した時の処理
    document.getElementById("waittitle").textContent = "PayPay";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "PayPay";
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            total: total,
            type: 'PayPay'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            referenceid = data.result.checkout.id;
            connect(1);
        } else {
            console.error('error');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("etc").addEventListener("click", function() {
    //その他を選択した時の処理
    document.getElementById("waittitle").textContent = "その他";
    document.getElementById("waittotal").textContent = "合計金額：¥"+total;
    document.getElementById("paywait").classList.remove("hidden");
    document.getElementById("payment").classList.add("hidden");
    paytype = "その他";
});

function connect(i){
    //ナンバーを手動で入力するように
    //プリンターがある場合は自動
    fetch('php/connect.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            referenceid: referenceid,
            callnumber: callnumber,
            status: i,
            printer:printer,
            paytype: paytype
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) { 
            orderid = data.id;
        }
    })
    .catch(error => console.error('Error:', error));
}