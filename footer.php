<?php

//at the end of page loading i clear the msg not to show it again
if(isset($_SESSION ['message'])){
    unset($_SESSION ['message']);
}
?>

    </body>
</html>
