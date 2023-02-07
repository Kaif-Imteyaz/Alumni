document.addEventListener('load',()=>{
    try{
        const session=JSON.parse(localStorage.getItem(("token")));
        console.log(session);
        fetch("http://localhost:8000/verifyToken",{
            method:"POST",
            body:JSON.stringify(session),
        }).then((response)=>{
            if(response.status!=200 && response.status!=201){
                localStorage.setItem("token",JSON.stringify(''));
                window.location.href="http://127.0.0.1:5500/index.html";
            }
            else{
                return response.json();
            }
        })
        .then((data)=>{
            loadPage(data);
        })
        .catch((e)=>{
            window.location.href="http://127.0.0.1:5500/index.html";
        })
    }
    catch(e){
        console.log(e);
        localStorage.setItem("token",JSON.stringify(''));
        window.location.href="http://127.0.0.1:5500/index.html";
    }
})



function loadPage(data){
    console.log(data);
    let logoutBtn=document.querySelector('#logout-btn');




    
    //event handler for logout
    logoutBtn.addEventListener('click',(e)=>{
        let session=JSON.parse(localStorage.getItem("token"));
        if(typeof(session)=="object" && session && Object.keys(session).length>0){
            let xhr=new XMLHttpRequest();
            let url=`http://localhost:8000/destroySession?id=${session.token}`;
            let method='DELETE';
            xhr.open(method,url);

            xhr.addEventListener("load",()=>{
                if(xhr.status===200){
                    localStorage.removeItem("token");
                    window.location="http://127.0.0.1:5500/index.html";
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
            window.location="http://127.0.0.1:5500/index.html";
        }
    })
}