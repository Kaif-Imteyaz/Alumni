document.addEventListener('DOMContentLoaded', function(event) {

        document.getElementById('signup').style.visibility = 'visible';
        document.getElementById('flip-card-btn-turn-to-back').style.visibility = 'visible';
        document.getElementById('flip-card-btn-turn-to-front').style.visibility = 'visible';


        document.getElementById('signup').onclick = function() {
          document.getElementById('flip-card').classList.toggle('do-flip');
          };

        document.getElementById('flip-card-btn-turn-to-back').onclick = function() {
        document.getElementById('flip-card').classList.toggle('do-flip');
        };

        document.getElementById('flip-card-btn-turn-to-front').onclick = function() {
        document.getElementById('flip-card').classList.toggle('do-flip');
        };


        let signupForm=document.querySelector('#register');

        signupForm.addEventListener('submit',function(e){
              e.preventDefault();
              let data={};
              for(let i=0;i<this.elements.length;i++){
                if(this.elements[i].type!='submit'){
                  data[this.elements[i].name]=this.elements[i].value;
                }
              }

              console.log(this.method);
              let jsonData=JSON.stringify(data);

              //sending the data to the backend using XMLHttpRequest
              let xhr=new XMLHttpRequest();
              xhr.open(this.method,this.action);

              xhr.addEventListener("load",()=>{
                console.log(xhr.response);
              })

              xhr.addEventListener('error',(err)=>{
                console.log(err);
              })

              xhr.send(jsonData);
        })

});
