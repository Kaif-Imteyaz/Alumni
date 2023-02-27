//for securing this page from authorized access
window.addEventListener('load', () => {
    const session = JSON.parse(localStorage.getItem(("token")));
    console.log(session);
    fetch("http://localhost:8000/verifyToken", {
        method: "POST",
        body: JSON.stringify(session),
    }).then((response) => {
        if (response.status != 200 && response.status != 201) {
            localStorage.setItem("token", JSON.stringify(''));
            window.location.href = "http://127.0.0.1:5501/login.html";
        }
        else {
            return response.json();
        }
    })
        .then((data) => {
            loadPage(data);
        })
        .catch((e) => {
            console.log(e);
            window.location.href = "http://127.0.0.1:5501/login.html";
        })
})



function loadPage(data){

    const logoutBtn=document.querySelector('#logout-btn');
    const uploadBtn=document.querySelector('#file-upload-btn');
    const userNameTag=document.querySelector("#userName");
    const userIdTag=document.querySelector("#userId");
    const userEmailTag=document.querySelector("#userEmail");
    const dropDownForm=document.querySelector('#dropdown');
    const fileUploadForm=document.querySelector('#fileUploadForm')

    const questionUploadForm=document.querySelector('#questionUploadForm');

    userNameTag.textContent=data.name;
    userIdTag.textContent=data.id;
    userEmailTag.textContent=data.email;

    fetchPdfFiles(dropDownForm);

    //fetching all the questions
    fetchQuestions(data.name);

    dropDownForm.addEventListener('submit',function(e){
        e.preventDefault();
        fetchPdfFiles(this);
    })

    questionUploadForm.addEventListener('submit',(e)=>{
        e.preventDefault();
        const question_title=document.querySelector('#question_title').value;
        const question_description=document.querySelector('#question_description').value;
        const askedBy=data.name;

        const url='http://localhost:8000/ask';
        const method='POST';
        const body={
            title:question_title,
            description:question_description,
            asked_by:askedBy,
        }

        fetch(url,{
            method:method,
            body:JSON.stringify(body),
        })
        .then((response)=>{
          response.text();
        })
        .then((data)=>{

        })
        .catch((e)=>{
            console.log(e);
        })
    })
   
    //event handler for logout
    logoutBtn.addEventListener('click', (e) => {
        console.log('clicked');
        let session = JSON.parse(localStorage.getItem("token"));
        if (typeof (session) == "object" && session && Object.keys(session).length > 0) {
            let xhr = new XMLHttpRequest();
            let url = `http://localhost:8000/destroySession?token=${session.token}`;
            let method = 'DELETE';
            xhr.open(method, url);

            xhr.addEventListener("load", () => {
                if (xhr.status === 200) {
                    localStorage.removeItem("token");
                    window.location = "http://127.0.0.1:5501/index.html";
                }
                else {
                    console.log(xhr.response);
                }
            })
            xhr.addEventListener("error", (err) => {
                console.log(err);
            })
            xhr.send();
        }
        else {
            window.location = "http://127.0.0.1:5501/index.html";
        }
    })

  
}
function fetchPdfFiles(_this) {
    const branch = _this.elements['branch'].value;
    const semester = _this.elements['semester'].value;
    const fileType = _this.elements['filetype'].value;
    fetch(`http://localhost:8000/getAllFiles?branch=${branch}&semester=${semester}&title=${fileType}`)
        .then((response) => {
            if (response.status != 200 && response.status != 201) {
                modifyTable();
            }
            else {
                return response.json();
            }
        })
        .then((data) => {
            if (data != null && typeof (data) == 'object' && Object.keys(data).length > 0) {
                displayFiles(data);
            }
        })
        .catch((e) => {
            console.log(e);
        })
}
function displayFiles(files) {
    modifyTable();
    const table = document.querySelector('#fileContent');
    files.forEach(file => {
        let tr = document.createElement('tr');
        let td1 = document.createElement('td');
        let td2 = document.createElement('td');
        let td3 = document.createElement('td');
        let td4 = document.createElement('td');
        let td5 = document.createElement('td');
        const aTag = document.createElement('a');
        td1.textContent = file['title'];
        tr.append(td1);
        td2.textContent = file['branch'];
        tr.append(td2);
        td3.textContent = file['semester'];
        tr.append(td3);
        td4.textContent = file['name'];
        tr.append(td4);
        aTag.href = `http://localhost:8000/getFile?id=${file['id']}`;
        aTag.target = "_blank"
        aTag.textContent = 'pdf';
        td5.append(aTag);
        tr.append(td5);
        table.append(tr);
    })
}
function modifyTable() {
    const table = document.querySelector('#fileContent');
    const tr = document.querySelectorAll('#fileContent tr');
    for (let i = 1; i < tr.length; i++) {
        table.removeChild(tr[i]);
    }
}


function fetchQuestions(contributer){
    const url="http://localhost:8000/getQues";
    const method='GET';
    fetch(url)
    .then((response)=>{
        return response.json();
    })   
    .then((data)=>{
        const outerDiv=document.querySelector('.faq-container')
        data.forEach(d=>{
            const div=document.createElement('div');
            div.classList.add('faq');
            // div.classList.add('active');

            const h3=document.createElement('h3');
            h3.classList.add('faq-title');
            h3.textContent=d.title;
            
            div.append(h3);
            
            const p=document.createElement('p');
            p.classList.add('faq-text');
            p.textContent=d.description;
            div.append(p);

            const p2=document.createElement('a');
            p2.style.cursor="pointer";
            p2.classList.add('faq-text');
            p2.classList.add('active');
            p2.textContent='reply';
            div.append(p2);

            const h4=document.createElement('h4');
            h4.classList.add('faq-text');
            h4.textContent="Answers";
            div.append(h4);

            const form=document.createElement('form');
            form.classList.add('qaform');
            form.classList.add('off');
            const input=document.createElement('input');
            form.append(input);
            const btn=document.createElement('button');
            btn.textContent="submit";
            btn.type="submit";
            btn.classList.add('qsubmit');
            form.append(btn);

            div.append(form);
            

            const button=document.createElement('button');
            button.classList.add('faq-toggle');
            
            const i1=document.createElement('i');
            const i2=document.createElement('i');

            i1.classList.add('fas');
            i1.classList.add('fa-chevron-down');

            i2.classList.add('fas');
            i2.classList.add('fa-times');

            button.appendChild(i1);
            button.appendChild(i2);


            div.append(button);
            button.addEventListener('click', () => {
                let answers=document.querySelectorAll('.ans'+d.id);
                if(answers.length>0){
                    answers.forEach(ans=>{
                        div.removeChild(ans);
                    })
                }

                button.parentNode.classList.toggle('active');
                //make request to fetch all ans related to the question.
              
                let xhr=new XMLHttpRequest();
                const url=`http://localhost:8000/fetchAns?quesId=${d.id}`;
                xhr.open('GET',url)
                xhr.addEventListener('load',()=>{
                    //display all the answers.
                    let response=JSON.parse(xhr.response);
                    response.forEach((res)=>{
                        const p=document.createElement('p');
                        p.classList.add('faq-text');
                        p.classList.add('ans'+d.id);
                        p.textContent=res.content;
                        div.append(p);
                    })
                });
                xhr.addEventListener('error',(e)=>{
                    console.log(e);
                })
                xhr.send();
            })
            p2.addEventListener('click',()=>{
                form.classList.toggle('off');
            })

            form.addEventListener('submit',(e)=>{
                e.preventDefault();
                let url=`http://localhost:8000/ans`;
                let method='POST';
                let payload={
                    content:input.value,
                    questionId:d.id,
                    contributer:contributer,
                }
                let xhr=new XMLHttpRequest();
                xhr.open(method,url);
                xhr.addEventListener('load',()=>{
                    console.log(xhr.response);
                 form.classList.toggle('off');
                })
                xhr.addEventListener('error',(e)=>{
                    console.log(e);
                })
                xhr.send(JSON.stringify(payload));
            })

            outerDiv.append(div);

        })
        // <div class="faq active">
        //         <h3 class="faq-title">
        //             Question
        //         </h3>
        //         <p class="faq-text">
        //             Question Description
        //         </p>
        //         <button class="faq-toggle">
        //             <i class="fas fa-chevron-down"></i>
        //             <i class="fas fa-times"></i>
        //         </button>
        //     </div>

    })
    .catch((e)=>{
        console.log(e);
    })
}