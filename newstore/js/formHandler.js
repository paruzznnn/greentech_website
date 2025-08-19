//============== Function =========================================
export function handleFormSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const fromUrl = form.dataset.url;
    const fromRedirect = form.dataset.redir;
    const fromType = form.dataset.type;
    const formData = new FormData(form);

    if (!form.checkValidity()) {
        alert("Please fill out the information completely.");
        return;
    }

    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    // console.log('data', data);
    // console.log('fromUrl', fromUrl);
    // console.log('fromRedirect', fromRedirect);
    // console.log('fromType', fromType);
    // return;

    fetch(fromUrl, {
        method: "POST",
        headers: {
            'Authorization': 'Bearer my_secure_token_123',
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
    .then((res) => res.json())
    .then((response) => {
        if(response.status){
          switch (fromType) {
            case "register":
              
              break;
            case "login":
              redirectPostForm(fromRedirect, { username: 'admin', password: '1234' });
              break;
            default:
              break;
          }
          
        }
    })
    .catch((error) => {
        console.error("error:", error);
    });

}

export function redirectGet(url, params = {}, target = '_self') {
  const query = new URLSearchParams(params).toString();
  const fullURL = query ? `${url}?${query}` : url;

  if (target === '_self') {
    window.location.href = fullURL;
  } else {
    window.open(fullURL, target);
  }
}

export function redirectPostForm(url, params = {}, target = '_self') {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = url;
  form.target = target;
  form.style.display = 'none';

  for (const key in params) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = key;
    input.value = params[key];
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}

export function redirectGetForm(url, params = {}, target = '_self') {
  const form = document.createElement('form');
  form.method = 'GET';
  form.action = url;
  form.target = target;
  form.style.display = 'none';

  for (const key in params) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = key;
    input.value = params[key];
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}

/*===================== Redirec function =============================
Exsample POST form
redirectPostForm('/login', { username: 'admin', password: '1234' });

Exsample GET query string
redirectGet('/search', { q: 'keyword', page: 2 });

Exsample GET form
redirectGetForm('/download', { file: 'report.pdf' }, '_blank');
=======================================================================*/

//============ Notify Alert ========================================
let notificationTimeout;
export function showNotification(message, type) {
    const notificationContainer = document.getElementById('notificationContainer');
    const notificationMessage = document.getElementById('notificationMessage');
    clearTimeout(notificationTimeout);
    notificationMessage.textContent = message;
    notificationMessage.className = 'notification-message ' + type;
    notificationContainer.classList.add('show');

    notificationTimeout = setTimeout(() => {
        notificationContainer.classList.remove('show');
        setTimeout(() => {
            notificationMessage.textContent = '';
            notificationMessage.className = 'notification-message';
        }, 300);
    }, 3000);
}

export function formatDateToDDMMYYYY(isoString) {
  if(isoString){
    const date = new Date(isoString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
  }else{
    return null;
  }
}

export function formatDateToYYYYMMDD(isoString) {
  if(isoString){
    const date = new Date(isoString);
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  }else{
    return null;
  }
}

