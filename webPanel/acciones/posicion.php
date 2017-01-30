<?php
include '../lib/variables.php';
?>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/jquery-1.12.3.js"></script>
<script src="../js/bootstrap.min.js"></script>

<body style=" background: #4CAF50;">
    <div class="col-md-12">
        <h2 class="text-center">Variables de posicion</h2>
        <div  class="col-xs-offset-2">
            <table style="width: 100%;">
                <tr>
                    <td width="25%" align="right">
                        <B>Aeropuerto</B> =   
                    </td>
                    <td >
                        <form method="GET" action="<?php echo $ip; ?>/position" target="resultados">
                            <div class="form-inline">
                                <input type=text name="altitude-ft[0]" class="form-control" placeholder="Altitud (en pies)">
                                <button type="submit "name="submit" value="set" >-></button>
                            </div>
                        </form>                         
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <B>Distancia</B> =   
                    </td>
                    <td align="left">
                        <div class="form-inline">
                            <form method="GET" action="<?php echo $ip; ?>/position" target="resultados">
                                <input type="text" name="latitude-deg" class="form-control" placeholder="Latitud (Deg)">
                                <button type="submit" value="set" name="submit">-></button>
                            </form>
                            </form>                        
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <B>Altitud</B> =    
                    </td>
                    <td align="left">
                        <form method="GET" action="<?php echo $ip; ?>/position" target="resultados">
                            <div class="form-inline">
                                <input type=text name="altitude-ft[0]" class="form-control" placeholder="Altitud">
                                <button type="submit" value="set" name="submit">-></button>
                            </div>
                        </form>                          
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <B>Airspeed</B> =    
                    </td>
                    <td align="left">
                        <div class="form-inline">
                            <form method="GET" action="<?php echo $ip; ?>/velocities" target="resultados">
                                <input type="text" name="airspeed-kt" class="form-control" placeholder="Rumbo">
                                <button type="submit" value="set" name="submit">-></button>
                            </form>  
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <B>Runway</B> =    
                    </td>
                    <td align="left">
                        <div class="form-inline">
                            <form method="GET" action="<?php echo $ip; ?>/orientation" target="resultados">
                                <input type="text" name="heading-deg" class="form-control" placeholder="Rumbo">
                                <button type="submit" value="set" name="submit">-></button>
                            </form>  
                        </div>
                    </td>
                </tr>
            </table>  
        </div>
    </div>
    <iframe name="resultados" width="0" height="0" frameborder="no"></iframe>
</body>