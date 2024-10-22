let id, reference_number, provide_status, date, orderid, ordercode, quantity, product_code, product_name, price;
extraction();
function extraction(){
    fetch('cash.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.status==='success'){
            $id = [];
            $reference_number = [];
            $provide_status = [];
            $date = [];
            $orderid = [];
            $ordercode = [];
            $quantity = [];
            $product_code = [];
            $product_name = [];
            $price = [];
            ({id,reference_number,provide_status,date} = data.orders);
            ({orderid,ordercode,quantity} = data.purchase);
            ({product_code,product_name,price} = data.products);
            display();
        }
    })
    .catch(error => console.error('Error:', error));
}

function display(){
    let table = document.getElementById("data");
    let count = 0;
    let text;
    text = document.getElementById("nonetext");
    text.textContent = "";
    //現在表示している行を削除する
    for(let i = table.rows.length - 1; 0 < i;i--){
        table.deleteRow(i);
    }
    //新しく行を作成し挿入する
    for(let i=0;i<id.length;i++){
        let index;
        let newtr = document.createElement("tr");
        let cell1 = document.createElement("td");
        if(!(id[i])){
            continue
        }
        cell1.textContent = id[i];
        cell1.id = "id"+i;

        index = id.indexOf(id[i]);
        let cell2 = document.createElement("td");
        if(!(reference_number[index])){
            continue
        }
        cell2.textContent = reference_number[index];
        let cell6 = document.createElement("td");
        cell6.textContent = date[index];

        index = product_code.indexOf(ordercode[i]);
        let cell3 = document.createElement("td");
        let total=0;
        for(let j=0;j<orderid.length;j++){
            if(id[i]==orderid[j]){
                let priceindex = product_code.indexOf(ordercode[j]);
                let mathtotal = quantity[j] * price[priceindex]
                total += mathtotal;
            }
        }
        cell3.textContent = total;
            
        let cell4 = document.createElement("td");
        let button = document.createElement("a");
        button.id = "print"+i;
        button.classList.add("printer");
        button.addEventListener("click", function() {
            let id = this.id.slice(-1);
            click(id);
        });
        button.textContent = "支払完了";
        cell4.appendChild(button);

        newtr.appendChild(cell1);
        newtr.appendChild(cell2);
        newtr.appendChild(cell3);
        newtr.appendChild(cell6);
        newtr.appendChild(cell4);
        table.appendChild(newtr);
        count++;
    }
    if(count==0){
        text = document.getElementById("nonetext");
        text.textContent = "条件に合うデータはありません";
    }
}

setInterval(extraction, 5000);

let ID;
function click(i){
    ID = i;
    connect(2);
    print();
}

function connect(i){
    fetch('connect.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            referenceid: reference_number[ID],
            status: i
        })
    })
    .then(response => response.json())
    .then(data => {
        
    })
    .catch(error => console.error('Error:', error));
}

function print(){
    let params = `?id=${id[ID]}`;
    let win = window.open(`Cprinter.php${params}`,"popupWindow","width=1px,height=1px");
}

function reback(){
    let backid = document.getElementById("print").value;
    fetch('connect.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            referenceid: backid,
            status: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        
    })
    .catch(error => console.error('Error:', error));
}