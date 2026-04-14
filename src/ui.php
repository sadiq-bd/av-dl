<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download videoes from any streaming site</title>
    <style>
        * {
            background-color: #222;
            color: #fff;
        }

        body,
        h2 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 400;
        }

        .container {
            text-align: center;
            margin: 20px auto;
            padding: 18px;
            border: 1px solid #6d6565;
            border-radius: 5px;
            max-width: 600px;
        }

        .input-contianer {
            margin: 8px;
            padding: 8px;
            width: 100%;

        }

        .input {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #6d6565;
            border-radius: 5px;
        }

        .flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .credit {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 12px;
            z-index: 99;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Any Video Downloader</h2>
        <form id="dwnldForm">
            <div class="input-container">
                <input id="urlInput" class="input" type="url" placeholder="Video URL" name="url" required>
            </div>
            <div class="input-container">
                <select class="input" name="quality" onchange="update_last_quality()">
                    <option value="" selected>Quality: Best</option>
                    <option value="1080">1080p</option>
                    <option value="720">720p</option>
                    <option value="480">480p</option>
                    <option value="360">360p</option>
                    <option value="240">240p</option>
                    <option value="144">144p</option>
                </select>
            </div>
            <div class="input-container">
                <select class="input" name="type" onchange="update_last_type()">
                    <option value="mp4" selected>Format: MP4</option>
                    <option value="mp3">MP3</option>
                </select>
            </div>
            <div class="input-container">
                <label><input type="radio" name="mode" value="0" checked> Direct download</label>
                <label><input type="radio" name="mode" value="1"> Save to server</label>
                <label><input type="radio" name="mode" value="2"> Save + download</label>

                <button class="input" type="submit" id="submitBtn" style="background: #0d06207b;">Continue</button>
            </div>
        </form>
        <br>
        <div class="input-container">
            <textarea id="stdout" class="input" rows="10" style="display:none; resize: vertical; background: #1e1e1e; color: #4AF626;" readonly></textarea>
        </div>
        <div class="input-container">
            <textarea id="stderr" class="input" rows="5" style="display:none; resize: vertical; background: #1e1e1e; color: #C50F1F;" readonly></textarea>
        </div>
    </div>
    <div class="credit">Made with ❤️ by <a href="https://sadiq.is-a.dev" target="_blank" style="text-decoration: none;">Sadiq</a></div>
</body>
<script>
    const formSubmitHandler = (formE) => {
        formE.preventDefault();

        const formData = new FormData(formE.target);
        const queryString = new URLSearchParams(formData).toString();

        disableForm(formE.target);
        document.getElementById('submitBtn').innerHTML = 'Downloading...';
        document.getElementById('urlInput').value = '';

        const stdout = document.getElementById('stdout');
        const stderr = document.getElementById('stderr');
        stdout.innerHTML = '';
        stdout.style.display = 'none';
        stderr.innerHTML = '';
        stderr.style.display = 'none';

        const source = new EventSource(`/download?${queryString}`);
        source.onmessage = (e) => {
            try {
                const resp = JSON.parse(e.data);
                switch (resp.type) {
                    case 'event':
                        if (resp.data === 'close') {
                            source.close();
                            enableForm(formE.target);
                            document.getElementById('submitBtn').innerHTML = 'Continue';
                        }
                        break;
                    case 'stdout':
                        stdout.style.display = 'block';
                        stdout.innerHTML += resp.data + "\n";
                        setTimeout(() => {
                            stdout.scrollTop = stdout.scrollHeight;
                        }, 100);
                        break;
                    case 'stderr':
                        stderr.style.display = 'block';
                        stderr.innerHTML += resp.data + "\n";
                        setTimeout(() => {
                            stderr.scrollTop = stderr.scrollHeight;
                        }, 100);
                        break;
                    case 'download_url':
                        if (!resp.data.match(/\?file=$/)) {
                            window.location = resp.data;
                        }
                        break;
                }
            } catch (err) {
                console.error(err);
            }
        };

    };

    const update_last_quality = () =>
        localStorage.setItem(
            'last_download_quality',
            document.querySelector('select[name="quality"]')?.value
        );

    const update_last_type = () =>
        localStorage.setItem(
            'last_download_type',
            document.querySelector('select[name="type"]')?.value
        );

    const disableForm = (form) => {
        form.onsubmit = (e) => { e.preventDefault(); };
    } ;

    const enableForm = (form) => {
        form.onsubmit = formSubmitHandler;
    }

    window.addEventListener('DOMContentLoaded', e => {

        document.querySelectorAll('input[name=mode]').forEach(m => {
            m.onchange = (e) => {
                localStorage.setItem(
                    'last_download_mode',
                    document.querySelector('input[name="mode"]:checked')?.value
                );
            };
        });


        if (localStorage.getItem('last_download_quality'))
            document.querySelector(`select[name="quality"]`).value = localStorage.getItem('last_download_quality');
        if (localStorage.getItem('last_download_type'))
            document.querySelector(`select[name="type"]`).value = localStorage.getItem('last_download_type');
        if (localStorage.getItem('last_download_mode'))
            document.querySelector(`input[name="mode"][value="${localStorage.getItem('last_download_mode')}"]`).checked = true;

        document.getElementById('dwnldForm').onsubmit = formSubmitHandler;
    });
</script>

</html>