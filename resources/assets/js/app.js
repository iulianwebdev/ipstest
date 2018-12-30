
/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');


// quick ajax call func
// in normal circumstances logic would be placed in other files
// and app.js left for loading the necessary pieces and firing the app

window.sendReminder = function(email, e){
    // skipping front-end validation
    axios.post('api/module_reminder_assigner', {
        contact_email: email
    }).then(({data}) => {
        
        if(data.message) {
            showMessage(data.message);
        }

    }, ({response}) => {

        if(response.data.message) {
            showMessage(response.data.message, true);
        }        
    });
}

function showMessage(message, error){

    let status = document.getElementById('status');

    if(error) {
        status.classList.add('alert-danger');
    } else {
        status.classList.remove('alert-danger');
    }
    status.textContent = message;
}
