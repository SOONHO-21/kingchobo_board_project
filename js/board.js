function getUrlParams() {
    const params = {};

    // [?&]: 하나 이상의 ? 또는 & 문자
    // [^=&]: = 또는 & 문자가 아닌 하나 이상의 문자 (키)
    // [^&]: = 뒤에 나오는 & 문자가 아닌 0개 이상의 문자 (값)
    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        // str: 매칭된 전체 문자열 (?name=John 또는 &age=30 등
        // key: 추출된 키 (name 또는 age)
        // value: 추출된 값 (John 또는 30)
        function(str, key, value) {
            params[key] = value;
        }
    );

    return params;
}

document.addEventListener("DOMContentLoaded", () => {
    // window.location.search 값
    const params = getUrlParams()

    // 글쓰기 버튼
    const btn_write = document.querySelector("#btn_write")
    btn_write.addEventListener("click", () => {
        self.location.href='./board_write.php?bcode=' + params['bcode'];
    })

    //검색 버튼
    const btn_search = document.querySelector("#btn_search")
    btn_search.addEventListener("click", () => {
        const sf = document.querySelector("#sf")    // sn: 선택 기준 항목
        const sn = document.querySelector("#sn")    // sf: 검색어
        if(sf.value == ''){
            alert('검색어를 입력합니다.')
            sf.focus()
            return false
        }

        self.location.href='./board.php?bcode='+ params['bcode'] +'&sn=' + sn.value + '&sf=' + sf.value;
    })

    // 전체 목록 버튼
    const btn_all = document.querySelector("#btn_all")
    btn_all.addEventListener("click", () => {
        self.location.href='./board.php?bcode=' + params['bcode']
    })

    //글 보기
    const trs = document.querySelectorAll(".tr");
    trs.forEach((box) => {      //board.php의 tr클래스를 가진 각 요소를 box라는 이름으로 받기로 함. NodeList의 각 요소
        box.addEventListener("click", () => {
            self.location.href='./board_view.php?bcode=' + params["bcode"] + '&idx=' + box.dataset.idx      //data-idx 값들이 box의 요소가 된다.
        })
    })
})