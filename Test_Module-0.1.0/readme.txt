Hi,

There are few notes related to test task.

Installation instractions: copy all files to appropriate folders on clean magento install version 1.9.2 community edition.
Google MPN input field will be displayed on product's options page at Edit Product section, General tab in admin interface.
According to the test task provided no core files were modified.
The task does not provide info on what pages Google MPN should be displayed. So, I made for both category and product pages.
Also, it was not clear why no core files should be modified according task description because this task does not require any
core files modifications. It does not require to rewrite any models as well. Core files modifications are forbidden in general.
Just for any case I did an extra job - I added observer to use event for header title modification on product info page to demonstrate
how to modify meta title or any data on output if necessary. Sorry for the extra job if that was not needed.

Please note, the module developed for default 'rwd' template and according to task description it will work 'out of the box'.
No 'rwd' default templates were modified but redefined only. So, it is safe to switch the module off after installation.

Please let me know if you have any questions or comments.

Thank you.