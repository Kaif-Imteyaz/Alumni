
function loadPdfContainer(url,filesContainer){
   
    let newIframe=document.createElement('iframe');
    newIframe.src=url;
    newIframe.height=400;
    newIframe.style="position:absolute;top:0;left:0;"
    filesContainer.append(newIframe);
    let div=document.createElement('div');
    div.style="poistion:absolute;top:0;right:0;z-index:5;";
    div.textContent="ehrewbriwqehr p";
    newIframe.append(div);

    // let pageNum=1;
    // let pageIsRendering=false;
    // let pageIsPending=null;

    // let canvas=document.createElement('canvas');
    // canvas.style="position:absolute;top:0;left:0;"
    // let ctx=canvas.getContext('2d');

   
    // const renderPage=(pdf)=>{
    //     // for(let i=1;i<=pdf.numPages;i++){
    //         pdf.getPage(1).then(page=>{
    //             pageIsRendering=true;
    //             let viewport=page.getViewport({scale:1});
    //             canvas.width=viewport.width;
    //             canvas.heigth=viewport.height;
    
    //             page.render({
    //                 canvasContext:ctx,
    //                 viewport:viewport, 
    //             }).promise.then(()=>{
    //                 pageIsRendering=true;
    //                 if(pageIsPending!=null){
    //                     renderPage(pageIsPending);
    //                     pageIsPending=null;
    //                 }
    //             })
    //         })
    //     // }
    //}

    // pdfjsLib.getDocument(url).promise.then((pdf)=>{
    //     renderPage(pdf);
    // })

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
            newDiv.innerHTML=`<div><h5>${file['title']}</h5></div><div><p id="pHover" style="font-weight:bolder;font-size:1em;color:#158247;text-align:center;display:none">${file['description']}</p></div><div><p>branch: ${file['branch']}</p><p> semester: ${file['semester']}</p><p>contributed by:<b>${file['name']}</b></p></div> `;
            filesContainer.append(newDiv);

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