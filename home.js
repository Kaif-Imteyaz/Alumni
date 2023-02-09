
function loadPdfContainer(url,filesContainer){
    let newDiv=document.createElement('div');
    newDiv.style="position:absolute;top:0;left:0;width:100%;height:100%";
    filesContainer.append(newDiv);

    let newIframe=document.createElement('iframe');
    newIframe.src=url;
    newIframe.style="width:100%;heigth:100%";
    newDiv.append(newIframe);
}



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
    let semester="6";
    let fileType="QuestionPaper";


    
    //load all the files from the database---> demo purposes
    fetch(`http://localhost:8000/getAllFiles?branch=${branch}&semester=${semester}&title=${fileType}`)
    .then((response)=>{
        if(response.status!=200 && response.status!=201){
            filesContainer.textContent="OOPS! NO Files FOUND!";
        }
        else{
            return response.json();
        }
    })
    .then((data)=>{
        data.forEach(file=>{
            let newDiv=document.createElement('div');
            newDiv.classList.add('file_box');
            newDiv.style="fontp"
            newDiv.textContent=file['title']+" for semester "+file['semester'];
            

            newDiv.addEventListener('click',(e)=>{
                fetch(`http://localhost:8000/getFile?id=${file['id']}`,)
                .then((response)=>{
                        if(response.status!=200 && response.status!=201){
                            console.log('something went wrong');
                        }
                        else{
                            return response.blob();
                        }
                })
                .then((data)=>{
                    let url=URL.createObjectURL(data);
                    loadPdfContainer(url,filesContainer);   
                })
                .catch((e)=>{
                    console.log(e);
                })
            })
            filesContainer.append(newDiv);
        })
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