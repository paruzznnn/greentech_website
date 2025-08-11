export function handleFormSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    if (!form.checkValidity()) {
        alert("Please fill out the information completely.");
        return;
    }

    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    fetch("/trandar_website/newstore/auth/check-login", {
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
            redirectPostForm('/trandar_website/newstore/user/', { username: 'admin', password: '1234' });
        }
    })
    .catch((error) => {
        console.error("error:", error);
    });

}

function redirectGet(url, params = {}, target = '_self') {
  const query = new URLSearchParams(params).toString();
  const fullURL = query ? `${url}?${query}` : url;

  if (target === '_self') {
    window.location.href = fullURL;
  } else {
    window.open(fullURL, target);
  }
}

function redirectPostForm(url, params = {}, target = '_self') {
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

function redirectGetForm(url, params = {}, target = '_self') {
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

// // GET แบบ query string
// redirectGet('/search', { q: 'keyword', page: 2 });

// // POST form ส่งข้อมูล login
// redirectPostForm('/login', { username: 'admin', password: '1234' });

// // GET ผ่าน form (เหมือนกดลิงก์แบบฟอร์ม)
// redirectGetForm('/download', { file: 'report.pdf' }, '_blank');