let id, reference_number, provide_status, date, orderid, ordercode, quantity, product_code, product_name, price;
extraction();
function extraction(){
    CheckoutID = document.getElementById("searchid").value;
    OrderID = document.getElementById("searchorder").value;
    fetch('control.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            CheckoutID: CheckoutID,
            OrderID: OrderID
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.status==='success'){
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
    //現在表示している行を削除する
    for(let i = table.rows.length - 1; 0 < i;i--){
        table.deleteRow(i);
    }
    //新しく行を作成し挿入する
    for(let i=0;i<orderid.length;i++){
        let index;
        let newtr = document.createElement("tr");

        let cell1 = document.createElement("td");
        cell1.textContent = orderid[i];

        index = id.indexOf(orderid[i]);
        let cell2 = document.createElement("td");
        cell2.textContent = reference_number[index];
        let cell5 = document.createElement("td");
        cell5.textContent = provide_status[index];
        let cell6 = document.createElement("td");
        cell6.textContent = date[index];

        index = product_code.indexOf(ordercode[i]);
        let cell3 = document.createElement("td");
        cell3.textContent = product_name[index];
        
        let cell4 = document.createElement("td");
        cell4.textContent = quantity[i];

        newtr.appendChild(cell1);
        newtr.appendChild(cell2);
        newtr.appendChild(cell3);
        newtr.appendChild(cell4);
        newtr.appendChild(cell5);
        newtr.appendChild(cell6);
        table.appendChild(newtr);
    }
}