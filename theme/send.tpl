<div style='width:100%;'>
        <ol id="menu" style="border-bottom:2px solid #e0e0e0;">
            <li>
               <a {$change}> ODEBRANE </a>
            </li>
            <li>
                <a href="#"> <b>WYSŁANE  </b> </a>
            </li>
        </ol>

        <div id='createMsg' class='phoneManagerButton' style='float:left;margin:10px;'>
            <div> Utwórz SMS </div> 
            <div > <img src='modules/CRM/Roundcube/theme/icon.png' /> </div>
        </div>
        <table id='' class='phoneManagerGreenTable'>
        <thead>
            <tr>
                <th>Data i czas wysłania</th>
                <th>Nadawca</th>
                <th>Odbiorca</th>
                <th colspan='3'></th>
                <th>Status</th>
            </tr>
        </thead>
        <tfoot>
            <tr >
                <td align="center" colspan="7"  >
                    <div style='text-align:center;' class="links">
                        {foreach from=$pages item=item key=key name=name}
                            {$item}
                        {/foreach}
                      </div>
                </td>
            </tr>
        </tfoot>
        <tbody>
            {foreach from=$records item=record key=key name=name}
                <tr class='text-decoded-sended'>
                    <td align='center' > {$record.SendingDateTime}</td>
                    <td align='center'  > {$record.CreatorID} </td>
                    <td align='center'> {$record.DestinationNumber} </td>
                    <td align='left' colspan='3'   style="padding-left:10px; padding-top:5px;padding-bottom:5px;">
                        <span style='display:block;'> {$record.TextDecoded|truncate:95} </span> 
                        <span style='display:none;'>  {$record.TextDecoded} </span>  
                    </td>
                    <td align='center'> {$record.Status} </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <div style=""> &nbsp; <br>&nbsp;<br>
    </div>
</div>
    <div id='smsCard' style='display:none;position:absolute;top:5%;background-color:#FAFAFA;width:60%;margin-right:20%;margin-left:20%;height:600px;border-radius:3% 3%;'>
        <div style='position:relative;width:100%;height:100%;'>
            <div style='position:absolute;right:5px;top:0px;' id='closeSmsBox' class='phoneManagerButton'> X </div>
            <h3>Wyślij SMS </h3><br>
            {$my_form_open}
                <b>{$my_form_data.contact.label} <br> (Nr telefonu lub imie i nazwisko)</b>   <br>
                {$my_form_data.contact.html}    <br>
                <b>{$my_form_data.message.label}</b>   <Br>
                {$my_form_data.message.html}    <Br>
                <b>{$my_form_data.submit.html} </b><br>

            {$my_form_close}
        </div>
    </div>


