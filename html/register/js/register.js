//宣言部(配列の見直し)
let MENU = [];
let category = [];  //カテゴリーを格納
let categoryquantity = [];  //カテゴリーごとの数を格納
let quantity = [];  //数を記憶する配列
let method = [];    //支払方法を格納
let total = 0;  //合計金額
let orderid; //オーダーID
let referenceid; //一意なID
let callid = 0; //呼び出し番号
let paytype;
let TOKEN;  //トークンを格納
let DEVICEID;   //デバイスIDを格納
let URL;;  //プリンターのIPアドレスを格納
let STORENAME;  //店名を格納

let pressbutton;
let interval;

//フラグ宣言部
let enablecount = true; //支払方法選択が出たときに数を固定
let checkoutflag = true;    //完了待機
let printer;    //プリンターが利用可能かどうか
let cashless = true; //決済端末を使うかどうか

setting();

function setting(){
    //jsonからデータを取得
    fetch('../../setting.json')
    .then(response => response.json())
    .then(data => {
        STORENAME = htmlspecialchars(data.STORENAME);
        category = data.Category;
        paytype = data.paytype;
        printer = data.printer;
        TOKEN = data.environment.TOKEN;
        DEVICEID = data.environment.DEVICE;
        URL = `http://${data.environment.PrinterIP}/cgi-bin/epos/service.cgi?devid=local_printer&timeout=10000`;
        for (let type in paytype) { //利用可能な支払方法のみ表示
            if (!paytype[type]) {
                document.getElementById(type).classList.remove("paybox");
                document.getElementById(type).classList.add("hidden");
            }
        }
        //weightの昇順に並び替える
        category.sort((a, b) => a.weight - b.weight);
        //商品情報を取ってくる
        fetch('php/GetProduct.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            product = data.product;
            category.forEach(category => {
                product.forEach(product => {
                  if (product.category_name === category.name && 0 < category.weight) {
                    MENU.push(product);
                  }
                });
            });
            MENU.forEach(product => {
                if (categoryquantity[product.category_name]) {
                  categoryquantity[product.category_name] += 1;
                } else {
                  categoryquantity[product.category_name] = 1;
                }
            });
            setproduct();
        })
        .catch(error => console.error('Error:', error));
    })
    .catch(error => console.error('Error', error));
}

function setproduct(){
    let menudiv = document.getElementById("menu");  //menudivを指定
    let operationdiv;   //操作中のdiv
    let countproduct = 0;   //現在のカテゴリの位置
    for(let i=0;i<MENU.length;i++){
        let title,div;
        quantity.push(0);   //quantity(数量を格納する配列)に0を追加
        let value;
        let nowvalue;
        let totalvalue = 0;
        for(let j=0;j<=countproduct;j++){   //カテゴリの境界線を確保
            value = Object.values(categoryquantity);
            nowvalue = value[j];
            totalvalue += nowvalue;
        }
        if (i === 0 || i === totalvalue) {  //カテゴリタイトルを追加
            title = document.createElement("h2");
            title.textContent = MENU[i].category_name;
            menudiv.appendChild(title);
            div = document.createElement("div");
            div.id = MENU[i].category_name;
            div.classList.add("grid");
            menudiv.appendChild(div);
            operationdiv = document.getElementById(MENU[i].category_name);
            if(i!=0){
                countproduct++;
            }
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
        productname.textContent = MENU[i].product_name;

        let productprice = document.createElement("p");
        productprice.classList.add("price");
        productprice.textContent = "¥"+MENU[i].price;

        let productstock = document.createElement("p");
        productstock.classList.add("stock");
        productstock.textContent = "在庫:"+MENU[i].stockpile+"個";
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
                        if(quantity[event.target.id]<MENU[event.target.id].stockpile){
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
        if(quantity[i]<MENU[i].stockpile){
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

//支払い完了ボタンを押したときの処理
document.getElementById("paycomplete").onclick = function(){
    complete();
}

//支払受付中に戻るボタンを押したときの処理
document.getElementById("payreturn").onclick = function(){
    if (confirm("支払い方法選択へ戻りますか？")) {
        document.getElementById("paywait").classList.add("hidden");
        document.getElementById("payment").classList.remove("hidden");
        cashless = true;
        fetch('php/cancel.php', {
            method: 'POST',
            headers:{
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                TOKEN: TOKEN,
                DEVICEID: DEVICEID,
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
    if(0 < total){
        cashless = false;
        document.getElementById("waittitle").textContent = "現金";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "現金";
        referenceid = randomstring(13); //一意IDの作成
        connect(1);
    }
});

document.getElementById("JCB").addEventListener("click", function() {
    //JCB/DinersClub/Discoverを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "JCB/Diners Club/Discover";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "クレジットカード";
        checkout("CARD_PRESENT");
    }
});

document.getElementById("Visa").addEventListener("click", function() {
    //Visa/Mastercard/Amexを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "Visa/Mastercard/Amex";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "クレジットカード";
        checkout("CARD_PRESENT");
    }
});

document.getElementById("QUICPay").addEventListener("click", function() {
    //QUICPayを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "QUICPay";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "QUICPay";
        checkout("FELICA_QUICPAY");
    }
});

document.getElementById("iD").addEventListener("click", function() {
    //iDを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "iD";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "iD";
        checkout("FELICA_ID");
    }
});

document.getElementById("IC").addEventListener("click", function() {
    //交通系ICを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "交通系IC";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "交通系IC";
        checkout("FELICA_TRANSPORTATION_GROUP");
    }
});

document.getElementById("PayPay").addEventListener("click", function() {
    //PayPayを選択した時の処理
    if(0 < total){
        document.getElementById("waittitle").textContent = "PayPay";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "PayPay";
        checkout("PayPay");
    }
});

document.getElementById("others").addEventListener("click", function() {
    //その他を選択した時の処理
    if(0 < total){
        cashless = false;
        document.getElementById("waittitle").textContent = "その他";
        document.getElementById("waittotal").textContent = "合計金額：¥"+total;
        document.getElementById("paywait").classList.remove("hidden");
        document.getElementById("payment").classList.add("hidden");
        paytype = "その他";
        referenceid = randomstring(13); //一意IDの作成
        connect(1);
    }
});

function checkout(type){
    fetch('php/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            TOKEN: TOKEN,
            DEVICEID: DEVICEID,
            total: total,
            type: type
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
}

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
            callnumber: callid,
            status: i,
            printer:printer,
            paytype: paytype
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) { 
            orderid = data.id;
            callid = data.callid;
        }
    })
    .catch(error => console.error('Error:', error));
}

//支払いが完了したかどうか確認する処理
function complete(){
    if(checkoutflag){
        if(cashless){
            checkoutflag = false;
            document.getElementById("paywait").classList.add("hidden");
            document.getElementById("waitingfor").classList.remove("hidden");
            fetch('php/complete.php', { //支払いが完了しているか確認
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    TOKEN: TOKEN,
                    DEVICEID: DEVICEID,
                    referenceid: referenceid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success'){
                    situation = data.result.checkout.status;
                    console.log("status:"+situation);
                    //支払が完了していたら～
                    if(situation==='COMPLETED'){
                        if(printer){
                            DelayProcess();
                        }else{
                            document.getElementById("waitingfor").classList.add("hidden");
                            document.getElementById("callidinput").classList.remove("hidden");
                        }
                    }else{
                        checkoutflag = true;
                        alert("未完了です");
                        document.getElementById("waitingfor").classList.add("hidden");
                        document.getElementById("paywait").classList.remove("hidden");
                    }
                } else {
                    checkoutflag = true;
                    alert("エラー");
                    document.getElementById("waitingfor").classList.add("hidden");
                    document.getElementById("paywait").classList.remove("hidden");
                }
            })
            .catch(error => {
                alert("エラー");
                checkoutflag = true;
                document.getElementById("waitingfor").classList.add("hidden");
                document.getElementById("paywait").classList.remove("hidden");
            });
        }else{
            if (confirm("支払いは完了していますか？")) {
                document.getElementById("paywait").classList.add("hidden");
                document.getElementById("waitingfor").classList.remove("hidden");
                if(printer){
                    DelayProcess();
                }else{
                    document.getElementById("waitingfor").classList.add("hidden");
                    document.getElementById("callidinput").classList.remove("hidden");
                }
            }
        }
    }
}

async function DelayProcess(){
    await connect(2);
    await order();
    await purchase();
}

//オーダー内容をpurchaseに書き込む
function order(){
    //商品コードを別配列に格納して受け渡す
    let productcode = [];
    MENU.forEach(product=>{
        productcode.push(product.product_code);
    });
    fetch('php/order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            orderid: orderid,
            productcode: productcode,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {})
    .catch(error => console.error('Error:', error));
}

//キーパッドのキーが押されたときの処理
function typed(e) {
    let txt = e.target.value;
    inputcell = document.getElementById("inputid");
    if(txt=="C"){
        inputcell.value = "";
    }else if(txt=="←"){
        newvalue = inputcell.value.slice(0,-1);
        inputcell.value = newvalue;
    }else{
        if(inputcell.value.length < 4){
            inputcell.value += txt;
        }
    }
}

//キーパッドの確定ボタンが押されたときの処理
document.getElementById("Confirmid").addEventListener("click", function() {
    if(0<document.getElementById("inputid").value.length){
        callid = document.getElementById("inputid").value;
        connect(2);
        order();
        purchase();
    }
});

//購入が完了したときの処理
function purchase(){
    document.getElementById("waitingfor").classList.add("hidden");
    document.getElementById("callidinput").classList.add("hidden");
    document.getElementById("purchase").classList.remove("hidden");
    postprinter();
    setTimeout(() => {
        location.reload();
    }, 8000);
}

//レシートを印刷する処理
async function postprinter(){
    let order = [];
    for (let i = 0; i < MENU.length; i++) {
        if(0<quantity[i]){
            order.push({ name: MENU[i].product_name, count: quantity[i] });
        }
    }
    //プリンターにリクエストを送る
    const req = new PrintRequest(callid,order);
    await req.join();
}


// receiptPrinter.js
//const URL = PrinterIP;
const TIMEOUT = 5000
/*

README

// 番号札の発行リクエスト
var req = new PrintRequest( 256, [{name:"hoge", count:1}, {name:"foo", count:1}, ...] )
//この時点で送信は完了している

await req.join() で、タイムアウト又はレスポンスが返ってくるまで待機できる
req.status でレスポンスのステータスコードが得られる　レスポンスがない場合は0
req.readyState で https://developer.mozilla.org/ja/docs/Web/API/XMLHttpRequest/readyState が取得できる。
なお追加で、-1でタイムアウトを表現している

*/

// Gen

class Numberreceipt{
    number;
    order = [];
    constructor(number, order){
        if(number==undefined || order==undefined){
            throw new Error('Parameter undefined');
        }else if(! order instanceof Object){
            throw new Error('order must be instanceof Object');
        }else if(!( order.length>0 && order[0] instanceof Object )){
            throw new Error('order object is invalid');
        }
        this.number = number;
        this.order = order;
    }

    get eposxml() {
        const req = 
            '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">' +
            '<s:Body>' +
            '<epos-print xmlns="http://www.epson-pos.com/schemas/2011/03/epos-print">' +
            '<text lang="ja" />'+
            '<text font="font_a"/>'+
            '<text align="center"/>'+
            '<feed unit="15"/>'+
            '<text dw="true" dh="true"/>'+
            '<text>◆ '+STORENAME+' ◆&#10;</text>'+
            '<text dw="false" dh="false"/>'+
            '<text>受け渡し用控え&#10;ご来店誠にありがとうございます&#10;</text>'+
            '<feed unit="15"/>'+
            '<text>------------------------------&#10;</text>'+
            '<feed unit="15"/>'+
            '<text align="center"/>'+
            this.formatArray(this.order)+
            '<feed unit="15"/>'+
            '<text>------------------------------&#10;</text>'+
            '<feed unit="15"/>'+
            '<text dw="true" dh="true"/>'+
            '<text font="font_b"/>'+
            '<text>お客様のご注文番号&#10;</text>'+
            '<text width="8" height="5"/>'+
            '<text font="font_a"/>'+
            '<text reverse="false" ul="false" em="false" color="color_1"/>'+
            '<text>' + this.number + '&#10;</text>'+
            '<feed unit="15"/>'+
            '<cut/>' +
            '</epos-print>'+
            '</s:Body>'+
            '</s:Envelope>';
        
        return req;
    }

    formatObjectToString(obj) {
        const name = obj.name;
        const count = obj.count.toString();
        
        const nameWidth = Array.from(name).reduce((width, char) => {
            return width + (char.match(/[ -~｡-ﾟ]/) ? 1 : 2);
        }, 0);
        const totalWidth = 30;
        const spacesNeeded = totalWidth - nameWidth - count.length;
        
        return '<text>' + name + ' '.repeat(spacesNeeded) + count + '点&#10;</text>';
    }

    formatArray(arr) {
        return arr.map(this.formatObjectToString).join('');
    }
}
// Request

class PrintRequest{

    status = 0;
    readyState;

    constructor(number, order){
        // [{name:"hoge", count:1}, {...}]
        if(number==undefined || order==undefined){
            throw new Error('Parameter undefined');
        }else if(! order instanceof Object){
            throw new Error('order must be instanceof Object');
        }else if(!( order.length>0 && order[0] instanceof Object )){
            throw new Error('order object is invalid');
        }

        const req = new Numberreceipt(number, order).eposxml;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', URL, true);
        xhr.setRequestHeader('Content-Type', 'text/xml; charset=utf-8');
        xhr.setRequestHeader('If-Modified-Since', 'Thu, 01 Jan 1970 00:00:00 GMT');
        xhr.setRequestHeader('SOAPAction', '""');
        xhr.onreadystatechange = () => {

            // Receive response document
            this.readyState = xhr.readyState;
            if (xhr.readyState == 4) {

                // Parse response document
                this.status = xhr.status
                if (xhr.status == 200) {
                    // alert(xhr.responseXML.getElementsByTagName('response')[0].getAttribute('success'));
                    console.log("success")
                }
                else {
                    // alert('Network error occured.');
                    console.log("failure")
                }
            }
        };
        xhr.timeout=TIMEOUT;
        xhr.ontimeout = () => {
            this.readyState = -1;
            // console.log("timeout")
        }

        xhr.send(req);
        console.log("send");
    }

    async join() {
        while (this.readyState !== 4 && this.readyState !== -1) {
            await new Promise(resolve => setTimeout(resolve, 100)); // 100ミリ秒待機
        }
        if (this.readyState == -1) return false;
        else return true;
    }
}

//不正な文字を変換
function htmlspecialchars(str){
    return (str + '').replace(/&/g,'&amp;')
                    .replace(/"/g,'&quot;')
                    .replace(/'/g,'&#039;')
                    .replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;')
                    .replace(/\\/g, '\\\\')
                    .replace(/\n/g, '\\n')
                    .replace(/\r/g, '\\r')
                    .replace(/\t/g, '\\t')
                    .replace(/\v/g, '\\v')
                    .replace(/\f/g, '\\f')
                    .replace(/\'/g, '\\\'')
                    .replace(/\"/g, '\\"')
                    .replace(/\x([0-9A-Fa-f]{2})/g, '\\x$1')
                    .replace(/\\u([0-9A-Fa-f]{4})/g, '\\u$1');
  }