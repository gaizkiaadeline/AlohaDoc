let alert = document.getElementById('error-notification')
let alertClose = document.getElementById('close-alert')

if(alertClose){
    alertClose.addEventListener('click',  function(){
        alert.style.display = "none";
    })
}

let successAlert = document.getElementById('success-notification')
let successAlertClose = document.getElementById('success-close-alert')

if(successAlertClose){
    successAlertClose.addEventListener('click', function(){
        successAlert.style.display = "none";
    })
}