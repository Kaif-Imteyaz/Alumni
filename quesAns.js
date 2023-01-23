import { initializeApp } from "https://www.gstatic.com/firebasejs/9.9.2/firebase-app.js";


// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyBjhNKgyqqMRJzACLySHAS7b6b0VDoXfic",
    authDomain: "seri-876dd.firebaseapp.com",
    databaseURL: "https://seri-876dd-default-rtdb.firebaseio.com",
    projectId: "seri-876dd",
    storageBucket: "seri-876dd.appspot.com",
    messagingSenderId: "598997278675",
    appId: "1:598997278675:web:dc162aaa0fd7e710c45926"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

import { getDatabase, ref, get, child }
    from "https://www.gstatic.com/firebasejs/9.9.2/firebase-database.js";

const db = getDatabase();
var DataSnapshot;
var data = [];

function getDataSnapShot() {
    const dbref = ref(db);
    

    get(child(dbref, '/Listings')).then((snapshot) => {
        if (snapshot != null) {
            DataSnapshot = snapshot.val();
            console.log(DataSnapshot);
            for(var i in DataSnapshot){
              data.push(i);
            }
            console.log(data);

            var dynamic = document.querySelector('.container');  
for (var i in data) {
    var dataToShow = DataSnapshot[data[i]];
    
  var fetch = document.querySelector('.container').innerHTML;  
  dynamic.innerHTML = `<div id="cards${i}" class="boxes">
      <div class="box-content">
        <h2>${dataToShow.Title}</h2>
        <label>Institute: ${dataToShow.Institute}</label><br>
        <label>Location : ${dataToShow.Location}</label><br>
        <label>Position : ${dataToShow.Position}</label><br>
        <label>SkillSet : ${dataToShow.SkillSet}</label><br>
        <a class="showmore" href="#">ReadMore</a>
      </div>
    </div>` + fetch ; 
    
}
        }
        else {
            alert("No data Found")
        }
    }).catch((error) => {
        alert(error)
    });

}




window.addEventListener('load', getDataSnapShot);










