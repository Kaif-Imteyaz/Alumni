

//for securing this page from authorized access
window.addEventListener('load', () => {
    const session = JSON.parse(localStorage.getItem(("token")));
    console.log(session);
    fetch("http://localhost:8000/verifyToken",{
        method:"POST",
        body:JSON.stringify(session),
    }).then((response)=>{
                if (response.status != 200 && response.status != 201) {
                    localStorage.setItem("token", JSON.stringify(''));
                    window.location.href = "http://127.0.0.1:5501/login.html";
                }
                else {
                    return response.json();
                }
        })
        .then((data)=>{
            loadPage(data);
        })
        .catch((e)=>{
            window.location.href="http://127.0.0.1:5501/login.html";
        })
})



function loadPage(data){

    const logoutBtn=document.querySelector('#logout-btn');
    const userNameTag=document.querySelector("#userName");
    const userIdTag=document.querySelector("#userId");
    const userEmailTag=document.querySelector("#userEmail");
    const dropDownForm=document.querySelector('#dropdown');


    userNameTag.textContent=data.name;
    userIdTag.textContent=data.id;
    userEmailTag.textContent=data.email;

    fetchPdfFiles(dropDownForm);

    dropDownForm.addEventListener('submit',function(e){
        e.preventDefault();
        fetchPdfFiles(this);
    })

   
    //event handler for logout
    logoutBtn.addEventListener('click',(e)=>{
        console.log('clicked');
        let session=JSON.parse(localStorage.getItem("token"));
        if(typeof(session)=="object" && session && Object.keys(session).length>0){
            let xhr=new XMLHttpRequest();
            let url=`http://localhost:8000/destroySession?token=${session.token}`;
            let method='DELETE';
            xhr.open(method,url);

            xhr.addEventListener("load",()=>{
                if(xhr.status===200){
                    localStorage.removeItem("token");
                    window.location="http://127.0.0.1:5501/index.html";
                }
                else{
                    console.log(xhr.response);
                }
            })
            xhr.addEventListener("error",(err)=>{
                console.log(err);
            })
            xhr.send();
        }
        else{
            window.location="http://127.0.0.1:5501/index.html";
        }
    })

}

function fetchPdfFiles(_this){
    const branch=_this.elements['branch'].value;
    const semester=_this.elements['semester'].value;
    const fileType=_this.elements['filetype'].value;

    fetch(`http://localhost:8000/getAllFiles?branch=${branch}&semester=${semester}&title=${fileType}`)
    .then((response)=>{
        if(response.status!=200 && response.status!=201){
            modifyTable();
        }
        else{
            return response.json();
        }
    })
    .then((data)=>{
        if( data!=null && typeof(data)=='object' && Object.keys(data).length>0){
            displayFiles(data);
        }
    })
    .catch((e)=>{
        console.log(e);
    })
}

function displayFiles(files){
    modifyTable();
    const table=document.querySelector('#fileContent');
    files.forEach(file=>{
        let tr=document.createElement('tr');
        let td1=document.createElement('td');
        let td2=document.createElement('td');
        let td3=document.createElement('td');
        let td4=document.createElement('td');
        let td5=document.createElement('td');
        const aTag=document.createElement('a');

        td1.textContent=file['title'];
        tr.append(td1);
        td2.textContent=file['branch'];
        tr.append(td2);
        td3.textContent=file['semester'];
        tr.append(td3);
        td4.textContent=file['name'];
        tr.append(td4);

        aTag.href=`http://localhost:8000/getFile?id=${file['id']}`;
        aTag.target="_blank"
        aTag.textContent='pdf';
        td5.append(aTag);
        tr.append(td5);

        table.append(tr);
    })
}

function modifyTable(){
    const table=document.querySelector('#fileContent');
    const tr=document.querySelectorAll('#fileContent tr');
    for(let i=1;i<tr.length;i++){
        table.removeChild(tr[i]);
    }
}