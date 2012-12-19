Send fax from asterisk
======================
Send FAX:
1) go to http://srvip/sendfax
2) load file (tif, jpg, pdf)
3) get code
4) call to recipient
5) recipient should transfer you to its local fax machine
6) transfer call to code number (stage 3)
7) Done
------------------------------------
Copy sendfax directory to www dir

cp sendfax/ /var/www/sendfax/
echo "Alias /sendfax /var/www/sendfax" > /etc/apache2/conf.d/sendfax.conf
chown -R www:www /var/www/sendfax/
------------------------------------
In extensions.conf

[send_fax]
exten => s,1,Answer
exten => s,n,Wait(2)
exten => s,n,SendFAX(/var/spool/asterisk/tmp/${NUM}.tif)

exten => h,1,System(/usr/bin/sendEmail.pl -f faxmachine@mail.com -t receiver@mail.com -u "Send fax" -m "sending fax" -a /var/spool/asterisk/tmp/${NUM}.tif)
exten => h,n,System(/bin/rm /var/spool/asterisk/tmp/${NUM}.tif)

[receive_fax]
exten => s,1,Answer
exten => s,n,Macro(fax_machine)

exten => h,1,System(${MAILCMD} -f ${FROMEMAILADDR} -t ${EMAILADDR} -bcc ${COPYEMAILADDR} -u "Incoming fax from ${CALLERID(num)}" -m ${MESSAGE} -a ${FAXFILENAME})
exten => h,n,NoOp(SYSTEMSTATUS : ${SYSTEMSTATUS})
exten => h,n,System(rm ${FAXFILENAME})
exten => h,n,NoOp(SYSTEMSTATUS : ${SYSTEMSTATUS})

[fax_machine]
exten => _881XX,1,Set(NUM=${EXTEN:2})
exten => _881XX,n,GoTo(send_fax,s,1)
