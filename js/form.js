document.querySelector('login.php').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('login.php', {
        method: 'POST',
        body: formData
    });
    const result = await res.text();
    alert(result);
});