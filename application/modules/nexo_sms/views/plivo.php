<script type="text/javascript" src="https://s3.amazonaws.com/plivosdk/web/plivo.min.js"/></script>
<script type="text/javascript">
"use strict";
// These two listeners are required before init
Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
Plivo.onReady = onReady;
Plivo.init();
// Credentials
var username = 'johndoe12345';
var pass = 'XXXXXXXX';
Plivo.conn.login(username, pass);
</script>