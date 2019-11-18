

<div style='width:100%;'>
        <ol id="menu" style="border-bottom:2px solid #e0e0e0;">
            <li>
               <a href="#"> <b>ODEBRANE </b> </a>
            </li>
            <li>
                <a {$change} > WYSŁANE  </a>
            </li>
        </ol>

        <table id='' class='phoneManagerGreenTable'>
            <thead>
                <tr>
                    <th>Data i czas wysłania</th>
                    <th>Nadawca</th>
                    <th colspan='3'></th>
                </tr>
            </thead>
            <tfoot>
                <tr >
                    <td align="center" colspan="5"  >

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
            {if $record->readed}
                 <tr class='text-decoded'>
            {else}
                 <tr class='text-decoded phoneManagerNoReaded' data-id='{$record->ID}'>
            {/if}
                    <td align='center'> {$record->ReceivingDateTime}</td>
                    <td align='center'> {$record->SenderNumber} </td>
                    <td colspan='3' align='left' style="width:80%;padding-left:10px; padding-top:5px;padding-bottom:5px;">
                        <span style='display:block;'> {$record->TextDecoded|truncate:95} </span> 
                        <span style='display:none;'>  {$record->TextDecoded} </span>  
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div style=""> &nbsp; <br>&nbsp;<br>
        </div>
</div>


