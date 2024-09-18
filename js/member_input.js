document.addEventListener("DOMContentLoaded", () => {

    //아이디 중복체크
    const btn_id_check = document.querySelector("#btn_id_check")
    btn_id_check.addEventListener("click", () => {
        const f_id = document.querySelector("#f_id")
        if(f_id.value == ''){
            alert('아이디를 입력해 주세요')
            return false
        }

        //AJAX
        const f1 = new FormData()
        f1.append('id', f_id.value)
        f1.append('mode', 'id_chk')

        const xhr = new XMLHttpRequest()
        xhr.open("POST", "./pg/member_process.php", true)
        xhr.send(f1)

        xhr.onload = () => {
            if(xhr.status == 200){
                const data = JSON.parse(xhr.responseText)
                if(data.result == 'success'){
                    alert('사용 가능한 아이디 입니다')
                    document.input_form.id_chk.value = "1"      //member_input.php의 <form name="input_form"아래 id_chk 부분
                } else if(data.result == 'fail') {
                    document.input_form.id_chk.value = "0"
                    alert('이미 사용중인 아이디입니다. 다른 아이디를 입력해 주세요.')
                    f_id.value = ''     //입력창을 비우고
                    f_id.focus()        //커서 올림
                } else if(data.result == 'empty_id') {
                    alert('아이디가 비어 있습니다.')
                    f_id.focus()
                }
            }
        }
    })

    //이메일 중복체크 및 이메일 형식 체크
    const btn_email_check = document.querySelector("#btn_email_check")
    btn_email_check.addEventListener("click", () => {
        const f_email = document.querySelector("#f_email")  //id로 접근
        if(f_email.value == ''){
            alert('이메일을 입력해 주세요')
            f_email.focus()
            return false
        }

        //AJAX
        const f1 = new FormData()
        f1.append('email', f_email.value)   //입력한 이메일
        f1.append('mode', 'email_chk')      //입력 모드와 이메일 중복 체크 여부

        const xhr = new XMLHttpRequest()
        xhr.open("POST", "./pg/member_process.php", "true")
        xhr.send(f1)

        xhr.onload = () => {
            if(xhr.status == 200){
                const data = JSON.parse(xhr.responseText)
                if(data.result == 'success'){
                    alert('사용 가능한 메일 입니다')
                    document.input_form.email_chk.value = "1"   //member_input.php의 <form name="input_form"아래 email_chk 부분
                } else if(data.result == 'fail') {
                    document.input_form.email_chk.value = "0"   //중복된 이메일
                    alert('이미 사용중인 이메일입니다. 다른 이메일을 입력해 주세요.')
                    f_email.value = ''
                    f_email.focus()
                } else if(data.result == 'empty_email') {
                    f_email.focus()
                    alert('이메일이 비어 있습니다.')
                } else if(data.result == 'email_format_wrong') {
                    alert('이메일이 형식이 맞지 않습니다.')
                    f_email.value = ''
                    f_email.focus()
                }
            }
        }
    })

    //가입 버튼 클릭시
    const btn_submit = document.querySelector("#btn_submit");
    btn_submit.addEventListener("click", () => {
        const f = document.input_form       //member_input.php의 <form name="input_form" 부분
        if(f.id.value == ''){
            alert('아이디를 입력해주세요')
            f.id.focus()
            return false
        }
        //아이디 중복확인 여부 체크
        if(f.id_chk.value == 0){
            alert('아이디 중복확인을 해주시기 바랍니다.')
            return false
        }

        //이름 입력 확인
        if(f.name.value == ''){
            alert('이름을 입력해주세요')
            f.name.focus()
            return false
        }

        //비밀번호 확인
        if(f.password.value == ''){
            alert('비밀번호를 입력해주세요.')
            f.password.focus()
            return false
        }

        if(f.password2.value == ''){
            alert('비밀번호 확인을 입력해주세요.')
            f.password2.focus()
            return false
        }

        //비밀번호 일치여부 확인
        if(f.password.value != f.password2.value){
            f.password.value = ''
            f.password2.value = ''
            f.password2.focus()
            return false
        }

        //이메일 입력 부분 확인
        if(f.email.value == '') {
            alert('이메일을 입력해 주세요.')
            f.email.focus()
            return false
        }
        //이메일 중복 체크 여부 확인
        if(f.email_chk.value == 0){
            alert('이메일 중복확인을 해주세요')
            return false
        }

        //우편번호 입력확인
        if(f.f_zipcode.value == ''){
            alert('우편번호를 입력해주세요')
            return false
        }
        //주소 입력 확인
        if(f.f_addr1.value == ''){
            alert('주소를 입력해주세요')
            return false
        }
        //상세 주소 입력 확인
        if(f.f_addr2.value == ''){
            alert('상세 주소를 입력해주세요')
            return false
        }

        f.submit()
    })

    //우편번호 찾기
    const btn_zipcode = document.querySelector("#btn_zipcode")
    btn_zipcode.addEventListener("click", () => {

        new daum.Postcode({
            oncomplete: function(data) {    //oncomplete: 비동기 작업이 완료되었을 때 호출되는 콜백 함수를 정의. 사용자가 주소를 선택한 후 실행

                console.log(data)   //콘솔에 데이터 출력
                let addr = ''
                let extra_addr = ''
                if(data.userSelectedType == 'J'){   //userSelectedType : 주소 타입
                    addr = data.jibunAddress    //지번
                }else if(data.userSelectedType == 'R'){
                    addr = data.roadAddress     //도로명
                }

                if(data.bname != ''){
                    extra_addr = data.bname     //동 이름
                }

                if(data.buildingName != ''){    //건물 이름이 있으면
                    if(extra_addr == ''){
                        extra_addr = data.buildingName  //extra_addr(상세주소)이 빌딩 이름
                    } else {    //건물 이름이 없으면
                        extra_addr += ', ' + data.buildingName
                    }
                }
                
                if(extra_addr != ''){   //상세 주소값이 있으면
                    extra_addr = '(' + extra_addr +')'  //extra_addr(상세주소)를 괄호에 넣기
                }

                const f_addr1 = document.querySelector("#f_addr1")      //주소
                f_addr1.value = addr + extra_addr       //상세 주소
                
                const f_zipcode = document.querySelector("#f_zipcode")  //우편 번호
                f_zipcode.value = data.zonecode

                const f_addr2 = document.querySelector("#f_addr2")  //상세 주소
                f_addr2.focus()
            }
        }).open();
    })

    const f_photo = document.querySelector("#f_photo")
    f_photo.addEventListener("change", (e) => {
        const reader = new FileReader()
        reader.readAsDataURL(e.target.files[0])

        reader.onload = function(event) {
            const f_preview = document.querySelector("#f_preview")
            f_preview.setAttribute("src", event.target.result)
        }
    })
})