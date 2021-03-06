 <!DOCTYPE html>
<html lang="ru">
<?php
header('Content-Type: text/html; charset=utf-8');
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заполните форму КП</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="stylesheet" href="style/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
    <script src="style/jquery/jquery-3.3.1.min.js"></script>
    <script async src="style/bootstrap/js/bootstrap.min.js"></script>
    <script src="localStorageDB.js"></script>
</head>

<body>
   <script>
       var cacheManager, 
           cacheFormInput,
           cacheMainTable, 
           cacheMainTableStringName='cacheMainTable',
           cacheExtraTable,
           cacheExtraTableStringName='cacheExtraTable',
           cacheCommentWrapper,
           onChahgeValueName = "value",
           comment = document.getElementsByName('comment'),
           mainCurChoose = document.getElementsByName('mainCurChoose'),
           extraCurChoose = document.getElementsByName('extraCurChoose');
       
       let cur_protocol = location.protocol
       let cur_host = location.host

       function cacheMake( key, value ) {
           localStorage.setItem(key, value);
       }
       
       function replaceComma(elem) {
           if (elem.value.indexOf(',') !== -1) {
               elem.value = elem.value.replace(',', '.');
           }
       }
       
       function showMainButtons(elem) {
           var buttons = document.getElementsByClassName('main_button');
           
           if (elem.value == 'michael_smu.jpg') {
               buttons[0].hidden = false;
           } else {
               buttons[0].hidden = true;
           }
       }
    </script>
    <div class="container p-0">
        <button class="main_button" hidden onclick="var win = window.open(`${cur_protocol}\/\/${cur_host}/managers/add_manager.php`, 'Добавить менеджера', 'left=400,top=300,width=500,height=300,menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes');" style="position:absolute; top:200px; left:-20%; padding:10px; cursor:pointer; border-radius:15px; border:0; z-index: 100;">Добавить / Удалить <br> менеджера</button>
        <header class="header">
            <div class="divHeader"><img class="header_img" src="style/header.jpg"></div>
        </header>
        <main class="content">
            <form id="KP" action="result.php" enctype="multipart/form-data" method="post">
                <h1>Заполни форму:</h1>
                <input type="hidden" name="maxFileSize" value="30000">
                
                <fieldset class="projects_list">
                    <legend>Выбери проект:</legend>
                    <input class="projects_search" type="search" placeholder="Поиск по проектам" oninput="findProject(this.value.toUpperCase());">
                    <div id="projects"></div>
                </fieldset>
                <script>
                    var existProjects = document.getElementById('projects'),
                        newProject = document.createElement('label'),
                        projectsList = [],
                        projectsContent = [],
                        projectsListCheck = false,
                        readyProjects = [];
                    
                    if (localStorage.getItem('projectsList') !== (undefined || null)) {
                        projectsList = localStorage.getItem('projectsList').split(',');
                        projectsContent = localStorage.getItem('projectsContent').split(',');
                        projectsListCheck = true;
                    }
                    
                    function setProject() {
                      if (projectsListCheck) {
                          newProject.className = 'projects_list-item';
                          
                          var idElem = projectsList.indexOf(localStorage.getItem('client'));
                          
                          for (var i=0; i<projectsList.length; i++) {
                            if (i == idElem) {
                                newProject.innerHTML = '<span class="deleteProject" style="color: #fff;">&mdash;</span>&nbsp;<input onclick="chooseProject(this);" name="currentProject" value="'+projectsList[i]+'" type="radio" checked>&nbsp;'+projectsList[i]+'<hr>';
                            } else {
                                newProject.innerHTML = '<span class="deleteProject" onclick="deleteProject(this)" title="Удалить проект">&mdash;</span>&nbsp;<input onclick="chooseProject(this);" name="currentProject" value="'+projectsList[i]+'" type="radio">&nbsp;'+projectsList[i]+'<hr>';
                            }
                            
                            existProjects.appendChild(newProject.cloneNode(true));
                          }
                          readyProjects = document.getElementsByClassName('projects_list-item');
                          return(readyProjects);
                      }
                    }
                    
                    function deleteProject(elem) {
                        var element = elem.nextElementSibling.value,
                            idElem = projectsList.indexOf(element);
                        obj_db.del(projectsList[idElem]);
                        projectsList.splice(idElem,1);
                        projectsContent.splice(idElem,1);
                        elem.parentNode.parentNode.removeChild(elem.parentNode);
                        cacheMake('projectsList', projectsList.toString());
                        cacheMake('projectsContent', projectsContent.toString());
                    }
                    
                    function saveProject() {
                        var storageCopy = Object.assign({}, localStorage);
                        storageCopy = JSON.stringify(storageCopy);
                        storageCopy = storageCopy.replace(/,/g, 'a1b2c3');
                        var idElem = projectsList.indexOf(localStorage.getItem('client'));
                        if (!(idElem != -1)) {
                            projectsList.push(localStorage.getItem('client'));
                            projectsContent.push(storageCopy);
                        } else {
                            projectsContent[idElem] = storageCopy;
                        }
                        localStorage.removeItem('projectsList');
                        localStorage.removeItem('projectsContent');
                        cacheMake('projectsList', projectsList.toString());
                        cacheMake('projectsContent', projectsContent.toString());
                    }
                    
                    function chooseProject(elem) {
                        var element = elem.value,
                            idElem = projectsList.indexOf(element),
                            contentElem = projectsContent[idElem];
                        
                        contentElem = contentElem.replace(/a1b2c3/g, ',');
                        contentElem = JSON.parse(contentElem);
                        
                        localStorage.clear();
                        
                        for (var prop in contentElem) {
                            cacheMake(prop, contentElem[prop]);
                        }
                        
                        cacheMake('projectsList', projectsList.toString());
                        cacheMake('projectsContent', projectsContent.toString());
                        location.reload();
                    } 
                    
                    readyProjects = setProject();
                    
                    function findProject(searchParam) {
                        var readyProjectsVal = [],
                            counterAlign = [];

                        for (var i=0; i<readyProjects.length; i++) {
                            readyProjectsVal[i] = readyProjects[i].innerText.toUpperCase().replace('—  ','');
                            if (readyProjectsVal[i].indexOf(searchParam) !== -1) {
                                counterAlign.push(i);
                            }
                        }
                        
                        for (var i=0; i<readyProjects.length; i++) {
                            if (counterAlign.indexOf(i) !== -1) {
                                readyProjects[i].style.display = 'block';
                            } else {
                                readyProjects[i].style.display = 'none';
                            }
                        }
                        
                        if (searchParam == '') {
                            for (var i=0; i<readyProjects.length; i++) {
                                readyProjects[i].style.display = 'block';
                            }
                        }
                    }
                </script>
                <fieldset>
                    <legend>Выбрать ФИО менеджера</legend>
                    <?php include 'managers/managers.htm';?>
                </fieldset><br>
                <label>Название компании, с которой работаете:
                    <input 
                        name="client" 
                        class="beginingForm" 
                        placeholder="Название компании или ИП"
                        onchange="cacheMake(this.name, this.value);"
                        onfocus="select(this)" 
                        type="text" 
                    >
                </label>
                <label>Имя контактного лица:
                    <input 
                        name="clientName" 
                        class="beginingForm" 
                        placeholder="Имя контактного лица"
                        onchange="cacheMake(this.name, this.value);" 
                        onfocus="select(this)" 
                        type="text" 
                    >
                </label><br>
                <label>Ссылка на коллекцию на сайте:
                    <input 
                        name="linkToItems" 
                        class="beginingForm" 
                        placeholder="Ссылка на страницу на сайте"
                        onchange="cacheMake(this.name, this.value);" 
                        onfocus="select(this)" 
                        type="url" 
                    >
                </label><br>
                <label>Введите текущий курс евро:
                    <input 
                        name="euroRate" 
                        class="beginingForm" 
                        placeholder="Введите текущий курс евро"
                        onchange="cacheMake(this.name, this.value);" 
                        onfocus="select(this)" 
                        type="text" 
                    >
                </label><br>
                <fieldset>
                    <legend>Выберите валюту основных материалов:</legend>
                    <label><input name="mainCurChoose" value="rub" class="" onchange="cacheMake(this.name, this.value);" type="radio" checked>&nbsp;Рубли</label><br>
                    <label><input name="mainCurChoose" value="rubAndEuro" class="" onchange="cacheMake(this.name, this.value);" type="radio">&nbsp;Рубли и Евро</label>
                </fieldset>
                <fieldset>
                    <legend>Выберите валюту дополнительных материалов:</legend>
                    <label><input name="extraCurChoose" value="rub" class="" onchange="cacheMake(this.name, this.value);" type="radio" checked>&nbsp;Рубли</label><br>
                    <label><input name="extraCurChoose" value="rubAndEuro" class="" onchange="cacheMake(this.name, this.value);" type="radio">&nbsp;Рубли и Евро</label>
                </fieldset>
                <fieldset>
                    <legend>Какие основные материалы:</legend>
                    <div style="position:relative;margin-bottom:20px;" id="mainPositions">
                        <div id="mainBlockItem1">
                            <label><input class="list-counter" name="mainMaterial[0, 0]" type="text" readonly value="1."></label>
                            <label><input class="list-name" name="mainMaterial[0, 1]" placeholder="Наименование" onchange="this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheMainTable', cacheMainTable.innerHTML);" type="text"></label>
                            <label><input class="list-number" name="mainMaterial[0, 2]" placeholder="Кол-во" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheMainTable', cacheMainTable.innerHTML);" type="text"></label>
                            <label>
                                <select name="mainMaterial[0, 3]" onchange="cacheMake(this.name, this.value); cacheMake('cacheMainTable', cacheMainTable.innerHTML);">
                                    <option value="шт.">шт.</option>
                                    <option value="м&sup2;" selected>м&sup2;</option>
                                    <option value="пог.м">пог.м</option>
                                </select>
                            </label>
                            <label><input class="list-price" name="mainMaterial[0, 4]" placeholder="Цена в рублях" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheMainTable', cacheMainTable.innerHTML);" title="Стоимость указывать в рублях даже в случае выбора пункта &#34;Рубли и евро&#34;" type="text"></label><br>
                        </div>
                        <div id="mainBlockItem2"></div>
                        <input title="Добавить позицию" type="button" value="+" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position:absolute;top:0;left:-50px;width:30px;" onclick="addMainPosition(); cacheMake('cacheMainTable', cacheMainTable.innerHTML);">
                        <input title="Удалить позицию" type="button" value="&ndash;" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position: absolute;top:32px;left:-50px;width:30px;" onclick="delMainPosition(); cacheMake('cacheMainTable', cacheMainTable.innerHTML);">
                    </div>
                </fieldset>
                <script>
                    var mainPositionsCountCols=0,
                        mainPosFullBlock,
                        mainPositionParent,
                        mainPositionsCount=1,
                        mainCounter=2;
                    
                    if (localStorage.getItem('mainPositionsCount')) {
                        mainPositionsCount = parseInt(localStorage.getItem('mainPositionsCount'));
                    }
                    if (localStorage.getItem('mainCounter')) {
                        mainCounter = parseInt(localStorage.getItem('mainCounter'));
                    }
                    
                    function addMainPosition() {
                        var mainPosFirstCol = '<label><input class="list-counter" name="mainMaterial['+mainPositionsCount+', '+mainPositionsCountCols+']" type="text" readonly value="'+(mainPositionsCount+1)+'."></label>';
                        var mainPosSecondCol = '<label><input class="list-name" name="mainMaterial['+mainPositionsCount+', '+(mainPositionsCountCols+1)+']" placeholder="Наименование" onchange="this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheMainTableStringName, cacheMainTable.innerHTML);" type="text"></label>';
                        var mainPosThirdCol = '<label><input class="list-number" name="mainMaterial['+mainPositionsCount+', '+(mainPositionsCountCols+2)+']" placeholder="Кол-во" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheMainTableStringName, cacheMainTable.innerHTML);" type="text"></label>';
                        var mainPosFourthCol = '<label> \
                                <select name="mainMaterial['+mainPositionsCount+', '+(mainPositionsCountCols+3)+']" onchange="cacheMake(this.name, this.value); cacheMake(cacheMainTableStringName, cacheMainTable.innerHTML);"> \
                                    <option value="шт.">шт.</option> \
                                    <option value="м&sup2;" selected>м&sup2;</option> \
                                    <option value="пог.м">пог.м</option> \
                                </select> \
                            </label>';
                        var mainPosFifthCol = '<label><input class="list-price"  name="mainMaterial['+mainPositionsCount+', '+(mainPositionsCountCols+4)+']" placeholder="Цена в рублях" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheMainTableStringName, cacheMainTable.innerHTML);" title="Стоимость указывать в рублях даже в случае выбора пункта &#34;Рубли и евро&#34;" type="text"></label><br>';
                        var nextMainElem = document.createElement('div');
                        nextMainElem.id = 'mainBlockItem'+(mainCounter+1);
                        
                        mainPosFullBlock = mainPosFirstCol +' '+ mainPosSecondCol +' '+ mainPosThirdCol +' '+ mainPosFourthCol +' '+ mainPosFifthCol;
                        
                        mainPositionParent = document.getElementById('mainBlockItem'+mainCounter).innerHTML += mainPosFullBlock;
                        
                        document.getElementById('mainBlockItem'+mainCounter).after(nextMainElem);
                        
                        mainCounter++;
                        mainPositionsCount++;
                        cacheMake('mainCounter', mainCounter);
                        cacheMake('mainPositionsCount', mainPositionsCount);
                    }
                    function delMainPosition() {
                        if (mainCounter == 1) {
                            alert("Больше нечего удалять!");
                            return;
                        }
                        
                        if (mainCounter != 1) {
                            document.getElementById('mainBlockItem'+mainCounter).remove();
                        }
                        mainCounter--;
                        document.getElementById('mainBlockItem'+mainCounter).remove();
                        cacheMake('mainCounter', mainCounter);
                        
                        if (mainCounter != 1) {
                            var nextMainElem = document.createElement('div');
                            nextMainElem.id = 'mainBlockItem'+(mainCounter);
                            
                            document.getElementById('mainBlockItem'+(mainCounter-1)).after(nextMainElem);
                        }
                        
                        mainPositionsCount--;
                        cacheMake('mainPositionsCount', mainPositionsCount);
                        
                        if (mainCounter == 1) {
                            var nextMainElem = '<div id="mainBlockItem1"></div>';
                            
                            document.getElementById('mainPositions').innerHTML += nextMainElem;
                        }
                    }
                </script>
                <fieldset>
                    <legend>Какие дополнительные материалы и работы:</legend>
                    <div style="position:relative;margin-bottom:20px;" id="additionalPositions">
                        <div id="blockItem1">
                            <label><input class="list-counter" name="additionalMaterial[0, 0]" type="text" readonly value="1."></label>
                            <label><input class="list-name" name="additionalMaterial[0, 1]" placeholder="Наименование" onchange="this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);" type="text"></label>
                            <label><input class="list-number" name="additionalMaterial[0, 2]" placeholder="Кол-во" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);" type="text"></label>
                            <label>
                                <select name="additionalMaterial[0, 3]" onchange="cacheMake(this.name, this.value); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);">
                                    <option value="шт." selected>шт.</option>
                                    <option value="м&sup2;">м&sup2;</option>
                                    <option value="пог.м">пог.м</option>
                                </select>
                            </label>
                            <label><input class="list-price" name="additionalMaterial[0, 4]" placeholder="Цена в рублях" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);" title="Стоимость указывать в рублях даже в случае выбора пункта &#34;Рубли и евро&#34;" type="text"></label><br>
                        </div>
                        <div id="blockItem2"></div>
                        <input title="Добавить позицию" type="button" value="+" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position:absolute;top:0;left:-50px;width:30px;" onclick="addAdditionalPositions(); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);">
                        <input title="Удалить позицию" type="button" value="&ndash;" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position: absolute;top:32px;left:-50px;width:30px;" onclick="delAdditionalPositions(); cacheMake('cacheExtraTable', cacheExtraTable.innerHTML);">
                    </div>
                </fieldset>
                <script>
                    var additionalPositionsCountCols=0,
                        additionalPosFullBlock,
                        additionalPositionParent,
                        additionalPositionsCount=1,
                        counter=2;
                    
                    if (localStorage.getItem('additionalPositionsCount')) {
                        additionalPositionsCount = parseInt(localStorage.getItem('additionalPositionsCount'));
                    }
                    if (localStorage.getItem('counter')) {
                        counter = parseInt(localStorage.getItem('counter'));
                    }
                    
                    function addAdditionalPositions() {
                        var additionalPosFirstCol = '<label><input class="list-counter" name="additionalMaterial['+additionalPositionsCount+', '+additionalPositionsCountCols+']" type="text" readonly value="'+(additionalPositionsCount+1)+'."></label>';
                        var additionalPosSecondCol = '<label><input class="list-name" name="additionalMaterial['+additionalPositionsCount+', '+(additionalPositionsCountCols+1)+']" placeholder="Наименование" onchange="this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheExtraTableStringName, cacheExtraTable.innerHTML);" type="text"></label>';
                        var additionalPosThirdCol = '<label><input class="list-number" name="additionalMaterial['+additionalPositionsCount+', '+(additionalPositionsCountCols+2)+']" placeholder="Кол-во" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheExtraTableStringName, cacheExtraTable.innerHTML);" type="text"></label>';
                        var additionalPosFourthCol = '<label> \
                                <select name="additionalMaterial['+additionalPositionsCount+', '+(additionalPositionsCountCols+3)+']" onchange="cacheMake(this.name, this.value); cacheMake(cacheExtraTableStringName, cacheExtraTable.innerHTML);"> \
                                    <option value="шт." selected>шт.</option> \
                                    <option value="м&sup2;">м&sup2;</option> \
                                    <option value="пог.м">пог.м</option> \
                                </select> \
                            </label>';
                        var additionalPosFifthCol = '<label><input class="list-price"  name="additionalMaterial['+additionalPositionsCount+', '+(additionalPositionsCountCols+4)+']" placeholder="Цена в рублях" onchange="replaceComma(this); this.setAttribute(onChahgeValueName, this.value); cacheMake(cacheExtraTableStringName, cacheExtraTable.innerHTML);" title="Стоимость указывать в рублях даже в случае выбора пункта &#34;Рубли и евро&#34;" type="text"></label><br>';
                        var nextElem = document.createElement('div');
                        nextElem.id = 'blockItem'+(counter+1);
                        
                        additionalPosFullBlock = additionalPosFirstCol +' '+ additionalPosSecondCol +' '+ additionalPosThirdCol +' '+ additionalPosFourthCol +' '+ additionalPosFifthCol;
                        
                        additionalPositionParent = document.getElementById('blockItem'+counter).innerHTML += additionalPosFullBlock;
                        
                        document.getElementById('blockItem'+counter).after(nextElem);
                        
                        counter++;
                        additionalPositionsCount++;
                        cacheMake('counter', counter);
                        cacheMake('additionalPositionsCount', additionalPositionsCount);
                    }
                    function delAdditionalPositions() {
                        if (counter == 1) {
                            alert("Больше нечего удалять!");
                            return;
                        }
                        
                        if (counter != 1) {
                            document.getElementById('blockItem'+counter).remove();
                        }
                        
                        counter--;
                        cacheMake('counter', counter);
                        document.getElementById('blockItem'+counter).remove();
                        
                        if (counter != 1) {
                            var nextElem = document.createElement('div');
                            nextElem.id = 'blockItem'+(counter);
                            
                            document.getElementById('blockItem'+(counter-1)).after(nextElem);
                        }
                        
                        additionalPositionsCount--;
                        cacheMake('additionalPositionsCount', additionalPositionsCount);
                        
                        if (counter == 1) {
                            var nextElem = '<div id="blockItem1"></div>';
                            
                            document.getElementById('additionalPositions').innerHTML += nextElem;
                        }
                    }
                </script>
                <fieldset class="comment_area">
                    <legend>Комментарий по доставке</legend>
                    <label><input name="comment" value="да" type="radio" checked onchange="cacheMake(this.name, this.value)">Доставка рассчитывается отдельно</label><br>
                    <label><input name="comment" value="нет" type="radio" onchange="cacheMake(this.name, this.value)">Нет комментария</label><br>
                    <label id="commentWrapper">
                        <input name="comment" value="свой" type="radio" onchange="cacheMake(this.name, this.value)">Другой комментарий(ии):<br>
                        <span>&mdash;&nbsp; </span><input class="comment-text" type="text" name="commentText[0]" onchange="cacheMake(this.name, this.value)">
                    </label>
                    <input title="Добавить позицию" type="button" value="+" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position:absolute;top:100px;left:-50px;width:30px;" onclick="addComment(); cacheMake('cacheCommentWrapper', cacheCommentWrapper.innerHTML);">
                    <input title="Удалить позицию" type="button" value="&ndash;" style="padding:5px 3px;font-size:30px;line-height:15px;vertical-align:top;position: absolute;top:132px;left:-50px;width:30px;" onclick="delComment(); cacheMake('cacheCommentWrapper', cacheCommentWrapper.innerHTML);">
                </fieldset>
                <br><input type="submit" onclick="cacheMake(); saveProject();" value="Создать КП" style="padding:5px;border-radius:10px;">
            </form>
        </main>
        <footer class="footer">
            <div class="divFooter"><img class="footer_img" src="style/footer.jpg"></div>
        </footer>
    </div>
    <script>
        var commentCount = 1;
        if (localStorage.getItem('commentCount')) {
            commentCount = localStorage.getItem('commentCount');
        }
        
        function addComment() {
            var input = document.createElement('input'),
                span = document.createElement('span'),
                br = document.createElement('br'),
                parent = document.getElementById('commentWrapper');
            
            span.innerHTML = '&mdash;&nbsp; ';
            input.className = 'comment-text';
            input.type = 'text';
            input.name = 'commentText['+commentCount+']';
            input.onchange = function(){cacheMake(this.name, this.value)};
            
            parent.appendChild(br);
            parent.appendChild(span);
            parent.appendChild(input);
            
            ++commentCount;
            cacheMake('commentCount', commentCount);
            return(commentCount);
        }
        
        function delComment() {
            var parent = document.getElementById('commentWrapper'),
                elem = parent.lastChild;
            
            if (elem.nodeName == "#text") {
                elem = elem.previousElementSibling;
            }
            
            elem.previousElementSibling.remove();
            elem.previousElementSibling.remove();
            elem.remove();
            
            if (commentCount !== 0) {
                --commentCount;
            }
            cacheMake('commentCount', commentCount);
            return(commentCount);
        }
        
        cacheManager = document.getElementsByName('chooseManager');
        cacheFormInput = document.getElementsByClassName('beginingForm');
        cacheMainTable = document.getElementById('mainPositions');
        cacheExtraTable = document.getElementById('additionalPositions');
        cacheCommentWrapper = document.getElementById('commentWrapper');
        
        function getCurCache() {
            cacheManager[0].value = localStorage.getItem(cacheManager[0].name);
            for (var i=0; i<cacheFormInput.length; i++) {
                cacheFormInput[i].value = localStorage.getItem(cacheFormInput[i].name);
            }
            
            if (localStorage.getItem('cacheMainTable')) {
                cacheMainTable.innerHTML = localStorage.getItem('cacheMainTable');
            }
            if (localStorage.getItem('cacheExtraTable')) {
                cacheExtraTable.innerHTML = localStorage.getItem('cacheExtraTable');
            }
            if (localStorage.getItem('cacheCommentWrapper')) {
                cacheCommentWrapper.innerHTML = localStorage.getItem('cacheCommentWrapper');
            }
            
            for (var i=0; i<document.getElementsByClassName('comment-text').length; i++) {
                document.getElementsByName('commentText['+i+']')[0].value = localStorage.getItem('commentText['+i+']');
            }
            
            if (localStorage.getItem('comment')) {
                for (var i=0; i<comment.length; i++) {
                    if (comment[i].value === localStorage.getItem('comment')) {
                        comment[i].checked = true;
                    }
                }
            }
            
            if (localStorage.getItem('mainCurChoose')) {
                for (var i=0; i<mainCurChoose.length; i++) {
                    if (mainCurChoose[i].value === localStorage.getItem('mainCurChoose')) {
                        mainCurChoose[i].checked = true;
                    }
                }
            }
            
            if (localStorage.getItem('extraCurChoose')) {
                for (var i=0; i<extraCurChoose.length; i++) {
                    if (extraCurChoose[i].value === localStorage.getItem('extraCurChoose')) {
                        extraCurChoose[i].checked = true;
                    }
                }
            }

            if (localStorage.getItem('mainPositionsCount')) {
                for (var i=0; i<mainPositionsCount; i++) {
                    if (localStorage.getItem('mainMaterial[' + i +', 3]')){
                        document.getElementsByName('mainMaterial[' + i +', 3]')[0].value = localStorage.getItem('mainMaterial[' + i +', 3]');
                    }
                }
            }

            if (localStorage.getItem('additionalPositionsCount')) {
                for (var i=0; i<additionalPositionsCount; i++) {
                    if (localStorage.getItem('additionalMaterial[' + i +', 3]')){
                        document.getElementsByName('additionalMaterial[' + i +', 3]')[0].value = localStorage.getItem('additionalMaterial[' + i +', 3]');
                    }
                }
            }

            if (localStorage.getItem('comment')) {
                for (var i=0; i<comment.length; i++) {
                    if (comment[i].value === localStorage.getItem('comment')) {
                        comment[i].checked = true;
                    }
                }
            }
        }
        
        getCurCache();
        showMainButtons(document.getElementsByName('chooseManager')[0]);
    </script>
</body>

</html>