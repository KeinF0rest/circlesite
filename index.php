<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>カレンダー</title>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="menu.js" defer></script>
        <link rel="stylesheet" href="style.css">
        <style>
            body {
                font-family: sans-serif;
                margin: 0;
            }
            
            .header-bar {
                margin: 20px;
                display: flex;
                justify-content: space-between;
            }
            
            .header-bar h1 {
                margin: 0;
                font-size: 24px;
            }
            
            #calendar {
                margin: 20px;
            }
            
            .fc .fc-toolbar-title {
                font-size: 24px;
            }
            
            .fc-prev-button,
            .fc-next-button {
                background-color: #4caf50 !important;
                border: none !important;
            }
            
            .fc-event {
                background-color: #4caf50;
                border: none;
                padding-left: 5px;
            }
            
            #event-info {
                margin: 20px;
            }
            
            #event-details a.event-card {
                margin: 0;
                display: block;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
                background-color: #fff;
                text-decoration: none;
                color: #333;
                transition: box-shadow 0.2s ease;
            }

            #event-details a.event-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            
            .event-title {
                margin-bottom: 10px;
                font-size: 16px;
            }
            
            .event-date {
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="header-bar">
            <h1>カレンダー</h1>
        </div>
    
        <div id="calendar"></div>
        
        <div id="event-info">
            <h3>イベント情報</h3>
            <div id="event-details">イベントをクリックするとイベント情報が表示されます。</div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ja',
                    events: 'load-event.php',
                    
                    eventClick: function (info) {
                        const event = info.event;
                        const title = event.title;
                        const start = event.start ? event.start.toLocaleDateString('ja-JP') : '';
                        const end = event.end ? event.end.toLocaleDateString('ja-JP') : '';
                        
                        let html = `
                            <a href="event-info.php?id=${event.id}" class="event-card">
                                <div class="event-title"><strong>${title}</strong></div>
                                <div class="event-date">${end ? `開催日: ${start} ～ ${end}` : `開催日: ${start}`}</div>
                            </a>
                        `;
                        document.getElementById('event-details').innerHTML = html;
                    }
                });
                calendar.render();
            });
        </script>
    </body>
</html>