Eds second strategy
===================
[Eds][1] second eds strategy.

Getting started
----------------
1. Install second Eds strategy:
   ```bash
   cd path_to_eds/Strategy
   git clone https://github.com/lyberteam/eds-second.git second
   ``

2. Configure Eds-second strategy with at least `Delimeter` and `App Secret`.

3. Direct user to `http://path_to_eds/second/?string=superlongstring`

Strategy configuration
----------------------

Required parameters:

```php
<?php
'Second' => array(
	'delimiter' => '|',
)
```

License
---------
Eds-second WTFPL License Licensed  
Copyright © 2017 Lyberteam 

[1]: https://github.com/lyberteam/eds
