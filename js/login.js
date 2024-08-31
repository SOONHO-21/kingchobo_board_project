document.addEventListener("DOMContentLoaded", () => {
    const btn_login = document.querySelector("#btn_login")
    btn_login.addEventListener("click", () => {
        const f_id = document.querySelector("#f_id")
        if(f_id.value == ''){
            alert('아이디를 입력하세요')
            f_id.focus()
            return false
        }
        const f_pw = document.querySelector("#f_pw")
        if(f_pw.value == ''){
            alert('비밀번호를 입력하세요')
            f_pw.focus()
            return false
        }

        //Ajax
        const xhr = new XMLHttpRequest()
        xhr.open("POST", "./pg/login_process.php", "true")      // ./pg/login_process.php의 로그인 로직 실행

        const f1 = new FormData()
        f1.append("id", f_id.value)
        f1.append("pw", f_pw.value)

        xhr.send(f1)

        xhr.onload = () => {
            if(xhr.status == 200) {
                const data = JSON.parse(xhr.responseText);      // JSON 형식의 응답을 JavaScript 객체로 변환
                if(data.result == 'login_fail') {
                    alert('해당 정보는 존재하지 않습니다.')
                    f_id.value = '';
                    f_pw.value = '';
                    f_id.focus()    // ID 입력창에 포커스
                    return false
                } else if(data.result == 'login_success') {     // ./pg/login_process.php의 로직에 의한 응답
                    alert('로그인에 성공했습니다.')
                    self.location.href='./index.php'   //로그인 성공시 홈페이지로 이동
                }
            } else {
                alert("통신에 실패했습니다. 다음에 다시 시도해주세요.");
            }
        }

    })
})