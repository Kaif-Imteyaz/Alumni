document.addEventListener('DOMContentLoaded', function() {

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

        
        function sendRequest(url,method,queryString,data,callback){

            data= typeof(data)=='object' && data!=null ? data : {};
            queryString=typeof(data)=='object' && queryString!=null ?queryString:{};
            
            //adding the querystring if available to the url
            if(Object.keys(queryString)>0){
              url+='?';
              for(let key in queryString){
                url+=key+"="+queryString[key];
                url+="&";
              }
            }

            let jsonData=JSON.stringify(data);
            
            let xhr=new XMLHttpRequest();
            xhr.open(method,url);
            
            xhr.addEventListener("load",()=>{
              callback(xhr.status,xhr.response);
            })
            
            xhr.addEventListener('error',(err)=>{
              console.log(err);
            })
            
            xhr.send(jsonData);
        }

        function formResponseHandler(formId,requestPayload,responsePayload){
            if(formId=='signup'){
              //login the user after they have successfully been registered.
              let data={
                "type":requestPayload.type,
                "email":requestPayload.email,
                "password":requestPayload.password,
              }
              sendRequest("http://localhost:8000/createSession","POST",undefined,data,(status,response)=>{
                  if(status!=200 && status!=201){
                      document.querySelector("#"+formId+" .formError").textContent=response;
                  }
                  else{
                    //we will receive the token
                  }
              })
            }

            if(formId=="login"){
                //we have received the token
                console.log(responsePayload);
            } 
        }
        
        //-----------------------------

        let forms=document.querySelectorAll('form');
        forms.forEach(form=>{
          form.addEventListener('submit',function(e){
            e.preventDefault();
            let data={};
            let method=this.method.toUpperCase();
            let url=this.action;
            let queryString={};

            for(let i=0;i<this.elements.length;i++){
              if(this.elements[i].type!='submit'){
                data[this.elements[i].name]=this.elements[i].value;
              }
            }
            
            //sending the data to the backend 
           sendRequest(url,method,queryString,data,(statusCode,response)=>{
              //all bad requests
                if(statusCode!=200 && statusCode!=201){
                    let error=typeof(response)=='string' && response.length<60?response:"an error has occured";
                    console.log(error);
                    document.querySelector('#'+this.id+" .formError").textContent=error.toUpperCase();
                }
                else{
                  formResponseHandler(this.id,data,response);   
                }
           });

      })
    })



})
