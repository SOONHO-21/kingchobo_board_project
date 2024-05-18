document.addEventListener("DOMContentLoaded", () => {

    const board_title = document.querySelector("#board_title")
    const btn_board_create = document.querySelector("#btn_board_create")

    btn_board_create.addEventListener("click", () => {

        if(board_title.value == "") {
            alert('게시판 이름을 입력해 주세요.')
            board_title.focus()
            return false
        }

        const xhr = new XMLHttpRequest()
        const f = new FormData()
        f.append('board_title', board_title.value)
        f.append('board_type', document.querySelector("#board_type").value)
        f.append('mode', 'input')

        xhr.open("POST", "./pg/board_process.php", true)
        xhr.send(f)
        xhr.onload = () => {
            if(xhr.status == 200) {
                const data = JSON.parse(xhr.responseText)
                if(data.result == 'mode_empty') {
                    alert('Mode 값이 누락되었습니다.')
                    return false
                } else if(data.result == 'title_empty') {
                    alert('게시판 명이 누락되었습니다.')
                    board_title.focus()
                    return false
                } else if(data.result == 'btype_empty') {
                    alert('게시판 타입이 누락되었습니다.')
                    return false
                } else if(data.result == 'success') {
                    alert('게시판이 생성되었습니다.')
                    self.location.reload()
                }
            } else {
                alert('통신 실패' + xhr.status)
            }
        }
    })


    //게시판 생성 버튼 클릭
    board_create_modal.addEventListener("click", () => {
        board_title.value = ''
    })
})