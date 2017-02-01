<div class="g4">

        <div id="eventCalendarShowDescription"></div>
        
       
        <script>
                $(document).ready(function() {
                        $("#eventCalendarShowDescription").eventCalendar({
                                eventsjson: 'json/agendaBarp.json.php',
                                showDescription: true,
                                monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
                                dayNames: [ 'Domingo','Lunes','Martes','Mi&eacute;rcoles',
                                        'Jueves','Viernes','Sabado' ],
                                dayNamesShort: [ 'Dom','Lun','Mar','Mie', 'Jue','Vie','Sab' ],
                                txt_noEvents: "No hay eventos para este periodo",
                                txt_SpecificEvents_prev: "",
                                txt_SpecificEvents_after: ":<br><br>",
                                txt_next: "siguiente",
                                txt_prev: "anterior",
                                txt_NextEvents: "Pr&oacute;ximos eventos:<br><br>",
                                txt_GoToEventUrl: "Ir al evento",
                                openEventInNewWindow: true,
                                jsonDateFormat: 'human'

                        });
                });
                            


        </script>

</div>