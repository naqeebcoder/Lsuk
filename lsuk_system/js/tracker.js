let inactivityTime = function() {
    let timeout;
    let timecounts=0;
    function resetTimer() {
        clearTimeout(timeout);
        timecounts=0;
        timeout = setTimeout(checkCount, 300000); // Timeout in milliseconds (e.g., 5 minutes)
    }
    function checkCount(){
        timecounts=timecounts+1;
        console.log(timecounts);
        if(timecounts==3){
            logout();
        }else{
            timeout = setTimeout(checkCount, 300000);
        }
    }
    function logout() {
        // alert("You have been logged out due to inactivity.");
        window.location='logout.php';
        // You can add code to redirect the user or log them out
    }
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
};
inactivityTime();
