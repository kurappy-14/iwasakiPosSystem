let food = [{name:"ぎょうざ",price:150}];
let drink = [{name:"生茶",price:50},{name:"カルピスウォーター",price:50},{name:"キリンレモン",price:50}];
let set = [];
let product = [];
SetProduct();
let amount = [];
let menu = product.length;
var total = 0;
let fluctuation = true;

Createmenu();

//メニュー欄の作成
function Createmenu(){
    let list = document.createElement("div");
    list.id = "list";
    document.body.appendChild(list);
    let title = document.createElement("h1");
    title.textContent = "メニュー";
    list.appendChild(title);
    title = document.createElement("h2");
    title.textContent = "料理";
    list.appendChild(title);
    for(let i=0;i<menu;i++){
        if((food.length)==i){
            title = document.createElement("h2");
            title.textContent = "ドリンク";
            list.appendChild(title);
        }else if((food.length+drink.length)==i){
            title = document.createElement("h2");
            title.textContent = "セットメニュー";
            list.appendChild(title);
        }
        let div = document.createElement("div");
        div.id = "menu";
        let productname = document.createElement("p");
        productname.textContent = product[i].name;
        div.appendChild(productname);        
        let productprice = document.createElement("p");
        productprice.textContent = product[i].price+"円";
        productprice.id = "price";
        div.appendChild(productprice);

        amount.push(0);

        let countdiv = document.createElement("div");
        countdiv.id = "count";
        div.appendChild(countdiv);

        let plus = document.createElement("span");
        plus.textContent = "＋";
        plus.id = i;
        plus.classList.add("plus");
        plus.addEventListener("click",function(event){
            increase(event.target.id);
        });
        countdiv.appendChild(plus);

        let count = document.createElement("input");
        count.id = "count"+i;
        count.classList.add("count");
        count.value = "0";
        count.addEventListener("input",function(event){
            change(event.target.id,event.target.value);
        });
        count.addEventListener("blur",function(event){
            if(event.target.value == "" || isNaN(event.target.value)){
                event.target.value = 0;
            }
            change(event.target.id,event.target.value);
        });
        countdiv.appendChild(count);

        let minus = document.createElement("span");
        minus.textContent = "ー";
        minus.id = i;
        minus.classList.add("minus");
        minus.addEventListener("click",function(event){
            decrease(event.target.id);
        });
        countdiv.appendChild(minus);

        list.appendChild(div);
    }
    let checkout = document.createElement("div");
    checkout.id = "checkout";
    list.appendChild(checkout);

    let totalprice = document.createElement("p");
    totalprice.textContent = "0円";
    totalprice.id = "total";
    checkout.appendChild(totalprice);

    let check = document.createElement("p");
    check.textContent = "お会計";
    check.id = "check";
    check.addEventListener("click",function(event){
        payment();
    });
    checkout.appendChild(check);
}

//プラスボタンを押したときの処理
function increase(i){
    if(fluctuation){
        amount[i]++;
        update();
    }
}

//マイナスボタンを押したときの処理
function decrease(i){
    if(fluctuation){
        if(0<amount[i]){
            amount[i]--;
        }
        update();
    }
}

//直接編集した時の処理
function change(i,value){
    if(0<=value){
        amount[i.slice(-1)] = value;
    }else{
        amount[i.slice(-1)] = 0;
    }
    update();
}

//数と配列amountを更新する
function update(){
    for(let i=0;i<menu;i++){
        document.getElementById("count"+i).value = amount[i];
    }
    total = 0;
    for(let i=0;i<menu;i++){
        total += product[i].price*amount[i];
    }
        document.getElementById("total").innerHTML = total+"円";
}

//モーダルウィンドウの閉じるボタンが押されたときの処理
document.getElementById("close").onclick = function(){
    document.getElementById("payment").classList.add("hidden");
    fluctuation = true;
}


function payment(){     //会計を押したときの処理
    document.getElementById("payment").classList.remove("hidden");
    fluctuation = false;
}

function sleep(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}

//ここから支払方法の処理(今は適当にalert入れてます)
//totalが購入金額(変更しても大丈夫です)
let flag = true;
let paymentid;

let cashconnect = true;
async function cash(){    //現金での支払い
    document.getElementById("payment").classList.add("hidden");
    document.getElementById("textdone").textContent = "支払いは完了しました";
    if(cashconnect){
        cashconnect = false;
        paymentid = randomstring(10);
        await sleep(2000);
        await connect(1);
        await sleep(2000);
        await connect(2);
        await postprinter();
        await document.getElementById("cashaffi").classList.add("hidden");
        await document.getElementById("done").classList.remove("hidden");
        await order();
        await donecash();
    }
}

function Credit(){  //クレジットカードまたはデビットカードでの支払い
    if(flag){
        flag = false;
        document.getElementById("waiting").classList.remove("hidden");
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                type: 'CARD_PRESENT'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success'){
                paymentid = data.result.checkout.id;
                console.log("id:"+paymentid);
                connect(1);
            } else {
                console.error('error');
            }
            flag = true;
        })
        .catch(error => console.error('Error:', error));
    }
}

function traffic(){ //交通系ICでの支払い
    if(flag){
        flag = false;
        document.getElementById("waiting").classList.remove("hidden");
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                type: 'FELICA_TRANSPORTATION_GROUP'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success'){
                paymentid = data.result.checkout.id;
                console.log("id:"+paymentid);
                connect(1);
            } else {
                console.error('error');
            }
            flag = true;
        })
        .catch(error => console.error('Error:', error));
    }
}

function QUICPay(){ //QUICPayでの支払い
    if(flag){
        flag = false;
        document.getElementById("waiting").classList.remove("hidden");
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                type: 'FELICA_QUICPAY'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success'){
                paymentid = data.result.checkout.id;
                console.log("id:"+paymentid);
                connect(1);
            } else {
                console.error('error');
            }
            flag = true;
        })
        .catch(error => console.error('Error:', error));
    }
}

function iD(){  //iDでの支払い
    if(flag){
        flag = false;
        document.getElementById("waiting").classList.remove("hidden");
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                type: 'FELICA_ID'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success'){
                paymentid = data.result.checkout.id;
                console.log("id:"+paymentid);
                connect(1);
            } else {
                console.error('error');
            }
            flag = true;
        })
        .catch(error => console.error('Error:', error));
    }
}

function PayPay(){    //PayPayでの支払い
    if(flag){
        flag = false;
        document.getElementById("waiting").classList.remove("hidden");
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                type: 'PAYPAY'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success'){
                paymentid = data.result.checkout.id;
                console.log("id:"+paymentid);
                connect(1);
            } else {
                console.error('error');
            }
            flag = true;
        })
        .catch(error => console.error('Error:', error));
    }
}

//支払いキャンセル
function cancel(){
    document.getElementById("waiting").classList.add("hidden");
    fetch('cancel.php', {
        method: 'POST',
        headers:{
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            paymentid: paymentid
        })
    })
    .then(response => response.json())
    .then(data => {
        connect(-1);
    })
    .catch(error => console.error('Error:', error));
}

let situation;

//完了ボタンを押したときの処理(支払が完了しているか確認する処理)
let completeorder = true;
function complete(){
    fetch('complete.php', {
        method: 'POST',
        headers:{
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            paymentid: paymentid
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success'){
            situation = data.result.checkout.status;
            console.log("status:"+situation);
            //支払が完了していたら～
            if(situation==='COMPLETED'){
                connect(2);
                order();
                postprinter();
                done();
            }
        } else {

        }
    })
    .catch(error => console.error('Error:', error));
}

//支払が完了した時の処理
function done(){
    document.getElementById("done").classList.remove("hidden");
    setTimeout(() => {
        location.reload();
    }, 8000);
}

let ordercode;

function connect(i){
    fetch('connect.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            referenceid: paymentid,
            status: i
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) { 
            ordercode = data.id;
        }
    })
    .catch(error => console.error('Error:', error));
}

function SetProduct(){
    /*
    fetch('SetProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product: product,
            price: price
        })
    })
    .then(response => response.json())
    .then(data => {

    })
    .catch(error => console.error('Error:', error));
    */
   for(let i=0;i<food.length;i++){
    product.push(food[i]);
   }
   for(let j=0;j<drink.length;j++){
    product.push(drink[j]);
   }
}

function order(){
    fetch('order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orderid: ordercode,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        cashconnect = true;
    })
    .catch(error => console.error('Error:', error));
}

function postprinter(){
    let order = [];
    for (let i = 0; i < product.length; i++) {
        if(0<amount[i]){
            order.push({ name: product[i].name, count: amount[i] });
        }
    }
    let params = `?order=${ordercode}&orderlist=${encodeURIComponent(JSON.stringify(order))}`;
    let win = window.open(`printer.php${params}`,"popupWindow","width=1px,height=1px");
}

function randomstring(length) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

function donecash(){
    setTimeout(() => {
        location.reload();
    }, 8000);
}

function cashcancel(){
    document.getElementById("cashaffi").classList.add("hidden");
    fetch('cancel.php', {
        method: 'POST',
        headers:{
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            paymentid: paymentid
        })
    })
    .then(response => response.json())
    .then(data => {
        connect(-1);
    })
    .catch(error => console.error('Error:', error));
}

function cashaffi(){
    document.getElementById("cashaffi").classList.remove("hidden");
}