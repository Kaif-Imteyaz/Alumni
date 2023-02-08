
function loadPage(data){
    const logoutBtn=document.querySelector('#logout-btn');
    const userNameTag=document.querySelector("#userName");
    const userIdTag=document.querySelector("#userId");
    const userEmailTag=document.querySelector("#userEmail");
    const filesContainer=document.querySelector("#files_container");

    userNameTag.textContent=data.name;
    userIdTag.textContent=data.id;
    userEmailTag.textContent=data.email;

    let branch="computerEngineering";
    let semester="8";
    let fileType="QuestionPaper";

    


    //load all the files from the database---> demo purposes
    fetch(`http://localhost:8000/getAllFiles?branch=${branch}&semester=${semester}&title=${fileType}`)
    .then((response)=>{
        if(response.status!=200 && response.status!=201){
            filesContainer.textContent="OOPS! NO Files FOUND!"
        }
        return response.json();
    })
    .then((data)=>{
        console.log(data);
    })
    .catch((e)=>{
        console.log(e);
    })


    //event handler for logout
    logoutBtn.addEventListener('click',(e)=>{
        let session=JSON.parse(localStorage.getItem("token"));
        if(typeof(session)=="object" && session && Object.keys(session).length>0){
            let xhr=new XMLHttpRequest();
            let url=`http://localhost:8000/destroySession?token=${session.token}`;
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