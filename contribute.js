

// //for securing this page from authorized access
window.addEventListener('load', () => {
    const session = JSON.parse(localStorage.getItem(("token")));
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
            console.log(e);
            window.location.href="http://127.0.0.1:5501/login.html";
        })
})



function loadPage(data){

    const logoutBtn=document.querySelector('#logout-btn');
 
    const userNameTag=document.querySelector("#userName");
    const userIdTag=document.querySelector("#userId");
    const userEmailTag=document.querySelector("#userEmail");

    const fileUploadForm=document.querySelector('#file-upload');


    userNameTag.textContent=data.name;
    userIdTag.textContent=data.id;
    userEmailTag.textContent=data.email;

 
   
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


    //file upload event handler
    fileUploadForm.addEventListener('submit',(e)=>{
            e.preventDefault();
            const name=data.name;
            const title=document.querySelector('#title').value;
            const semester=document.querySelector('#semester').value;
            const description=document.querySelector('#description').value;
            const branch=document.querySelector('#branch').value;
            const fileInput=document.querySelector('#file');
            const action=fileUploadForm.action;
            const method=fileUploadForm.method;

            const formData=new FormData();

            formData.append("file",fileInput.files[0]);
            formData.append('name',name);
            formData.append('title',title);
            formData.append('semester',semester);
            formData.append('description',description);
            formData.append('branch',branch);
            
            fetch(action,{
                method:method,
                body:formData,
            })
            .then((response)=>{
                return response.text();
            })
            .then((data)=>{
                const formError=document.querySelector('.formError');
                formError.textContent=data;           
             })
           .catch((e)=>{
                console.log(e);
            })

    })
}

