#!/usr/bin/env php
<?php echo password_hash(stream_get_contents(STDIN), PASSWORD_DEFAULT);