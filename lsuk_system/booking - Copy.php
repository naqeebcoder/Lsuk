<?php include'db.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    </head>
<body>    
        <form action="" class="register">
          <h1>Place an Order</h1>
          <fieldset class="row1">
            <legend>Work Details
          </legend>
                <p>
                    <label>Source  Language *
                    </label>
                     <select name="dialect" required=''>
                     <option value="">..Select..</option>
                    <option>Afrikaans</option>
<option>Albanian</option>
<option>Arabic</option>
<option>Armenian</option>
<option>Basque</option>
<option>Bengali</option>
<option>Bulgarian</option>
<option>Catalan</option>
<option>Cambodian</option>
<option>Chinese (Mandarin)</option>
<option>Croatian</option>
<option>Czech</option>
<option>Danish</option>
<option>Dutch</option>
<option>English</option>
<option>Estonian</option>
<option>Fiji</option>
<option>Finnish</option>
<option>French</option>
<option>Georgian</option>
<option>German</option>
<option>Greek</option>
<option>Gujarati</option>
<option>Hebrew</option>
<option>Hindi</option>
<option>Hungarian</option>
<option>Icelandic</option>
<option>Indonesian</option>
<option>Irish</option>
<option>Italian</option>
<option>Japanese</option>
<option>Javanese</option>
<option>Korean</option>
<option>Latin</option>
<option>Latvian</option>
<option>Lithuanian</option>
<option>Macedonian</option>
<option>Malay</option>
<option>Malayalam</option>
<option>Maltese</option>
<option>Maori</option>
<option>Marathi</option>
<option>Mongolian</option>
<option>Nepali</option>
<option>Norwegian</option>
<option>Persian</option>
<option>Polish</option>
<option>Portuguese</option>
<option>Punjabi</option>
<option>Quechua</option>
<option>Romanian</option>
<option>Russian</option>
<option>Samoan</option>
<option>Serbian</option>
<option>Slovak</option>
<option>Slovenian</option>
<option>Spanish</option>
<option>Swahili</option>
<option>Swedish</option>
<option>Tamil</option>
<option>Tatar</option>
<option>Telugu</option>
<option>Thai</option>
<option>Tibetan</option>
<option>Tonga</option>
<option>Turkish</option>
<option>Ukrainian</option>
<option>Urdu</option>
<option>Uzbek</option>
<option>Vietnamese</option>
<option>Welsh</option>
<option>Xhosa</option>
                    </select>
                    <label>Target  Language *
                    </label>
                    <select name="dialect2" required=''>
                      <option value="">..Select..</option>
                      <option>Afrikaans</option>
                      <option>Albanian</option>
                      <option>Arabic</option>
                      <option>Armenian</option>
                      <option>Basque</option>
                      <option>Bengali</option>
                      <option>Bulgarian</option>
                      <option>Catalan</option>
                      <option>Cambodian</option>
                      <option>Chinese (Mandarin)</option>
                      <option>Croatian</option>
                      <option>Czech</option>
                      <option>Danish</option>
                      <option>Dutch</option>
                      <option>English</option>
                      <option>Estonian</option>
                      <option>Fiji</option>
                      <option>Finnish</option>
                      <option>French</option>
                      <option>Georgian</option>
                      <option>German</option>
                      <option>Greek</option>
                      <option>Gujarati</option>
                      <option>Hebrew</option>
                      <option>Hindi</option>
                      <option>Hungarian</option>
                      <option>Icelandic</option>
                      <option>Indonesian</option>
                      <option>Irish</option>
                      <option>Italian</option>
                      <option>Japanese</option>
                      <option>Javanese</option>
                      <option>Korean</option>
                      <option>Latin</option>
                      <option>Latvian</option>
                      <option>Lithuanian</option>
                      <option>Macedonian</option>
                      <option>Malay</option>
                      <option>Malayalam</option>
                      <option>Maltese</option>
                      <option>Maori</option>
                      <option>Marathi</option>
                      <option>Mongolian</option>
                      <option>Nepali</option>
                      <option>Norwegian</option>
                      <option>Persian</option>
                      <option>Polish</option>
                      <option>Portuguese</option>
                      <option>Punjabi</option>
                      <option>Quechua</option>
                      <option>Romanian</option>
                      <option>Russian</option>
                      <option>Samoan</option>
                      <option>Serbian</option>
                      <option>Slovak</option>
                      <option>Slovenian</option>
                      <option>Spanish</option>
                      <option>Swahili</option>
                      <option>Swedish</option>
                      <option>Tamil</option>
                      <option>Tatar</option>
                      <option>Telugu</option>
                      <option>Thai</option>
                      <option>Tibetan</option>
                      <option>Tonga</option>
                      <option>Turkish</option>
                      <option>Ukrainian</option>
                      <option>Urdu</option>
                      <option>Uzbek</option>
                      <option>Vietnamese</option>
                      <option>Welsh</option>
                      <option>Xhosa</option>
                    </select>
                </p>
            <p>
              <label>Assignment  Date *
                    </label>
                    <input type="date" name="words" required='' style="border:1px solid #CCC" />
                    <label>Assignment  Time *
                    </label>
               <input type="time" name="refrence" required='' style="border:1px solid #CCC" />
              </p>
              <p>
                  <label>Assignment  Duration *
                </label>
                  <input type="number" name="hour" required='' style="border:1px solid #CCC" />
                  <label>Name/Case Ref No *
                  </label>
                  <input name="mile" type="text" pattern="[0-9]"/>
                  <!--
                  <label class="obinfo">* obligatory fields-->
                  </label>
            </p>
          </fieldset>
          <fieldset class="row1">
            <legend>Assignment Location
          </legend>
            <p>
              <label class="optional">Building No / Name
              </label>
                    <input type="text" name="words" required='' pattern="[0-9]"/>
                    <label class="optional">Street / Road
                    </label>
               <input type="text" name="refrence" required=''/>
            </p>
            <p>
                  <label class="optional">City
                </label>
                  <select name="city2">
                    <option>--Select--</option>
                    <optgroup label="England">
                      <option>Bedfordshire</option>
                      <option>Berkshire</option>
                      <option>Bristol</option>
                      <option>Buckinghamshire</option>
                      <option>Cambridgeshire</option>
                      <option>Cheshire</option>
                      <option>City of London</option>
                      <option>Cornwall</option>
                      <option>Cumbria</option>
                      <option>Derbyshire</option>
                      <option>Devon</option>
                      <option>Dorset</option>
                      <option>Durham</option>
                      <option>East Riding of Yorkshire</option>
                      <option>East Sussex</option>
                      <option>Essex</option>
                      <option>Gloucestershire</option>
                      <option>Greater London</option>
                      <option>Greater Manchester</option>
                      <option>Hampshire</option>
                      <option>Herefordshire</option>
                      <option>Hertfordshire</option>
                      <option>Isle of Wight</option>
                      <option>Kent</option>
                      <option>Lancashire</option>
                      <option>Leicestershire</option>
                      <option>Lincolnshire</option>
                      <option>Merseyside</option>
                      <option>Norfolk</option>
                      <option>North Yorkshire</option>
                      <option>Northamptonshire</option>
                      <option>Northumberland</option>
                      <option>Nottinghamshire</option>
                      <option>Oxfordshire</option>
                      <option>Rutland</option>
                      <option>Shropshire</option>
                      <option>Somerset</option>
                      <option>South Yorkshire</option>
                      <option>Staffordshire</option>
                      <option>Suffolk</option>
                      <option>Surrey</option>
                      <option>Tyne and Wear</option>
                      <option>Warwickshire</option>
                      <option>West Midlands</option>
                      <option>West Sussex</option>
                      <option>West Yorkshire</option>
                      <option>Wiltshire</option>
                      <option>Worcestershire</option>
                    </optgroup>
                    <optgroup label="Scotland">
                      <option>Aberdeenshire</option>
                      <option>Angus</option>
                      <option>Argyllshire</option>
                      <option>Ayrshire</option>
                      <option>Banffshire</option>
                      <option>Berwickshire</option>
                      <option>Buteshire</option>
                      <option>Cromartyshire</option>
                      <option>Caithness</option>
                      <option>Clackmannanshire</option>
                      <option>Dumfriesshire</option>
                      <option>Dunbartonshire</option>
                      <option>East Lothian</option>
                      <option>Fife</option>
                      <option>Inverness-shire</option>
                      <option>Kincardineshire</option>
                      <option>Kinross</option>
                      <option>Kirkcudbrightshire</option>
                      <option>Lanarkshire</option>
                      <option>Midlothian</option>
                      <option>Morayshire</option>
                      <option>Nairnshire</option>
                      <option>Orkney</option>
                      <option>Peeblesshire</option>
                      <option>Perthshire</option>
                      <option>Renfrewshire</option>
                      <option>Ross-shire</option>
                      <option>Roxburghshire</option>
                      <option>Selkirkshire</option>
                      <option>Shetland</option>
                      <option>Stirlingshire</option>
                      <option>Sutherland</option>
                      <option>West Lothian</option>
                      <option>Wigtownshire</option>
                    </optgroup>
                    <optgroup label="Wales">
                      <option>Anglesey</option>
                      <option>Brecknockshire</option>
                      <option>Caernarfonshire</option>
                      <option>Carmarthenshire</option>
                      <option>Cardiganshire</option>
                      <option>Denbighshire</option>
                      <option>Flintshire</option>
                      <option>Glamorgan</option>
                      <option>Merioneth</option>
                      <option>Monmouthshire</option>
                      <option>Montgomeryshire</option>
                      <option>Pembrokeshire</option>
                      <option>Radnorshire</option>
                    </optgroup>
                    <optgroup label="Northern Ireland">
                      <option>Antrim</option>
                      <option>Armagh</option>
                      <option>Down</option>
                      <option>Fermanagh</option>
                      <option>Londonderry</option>
                      <option>Tyrone</option>
                    </optgroup>
                  </select>
                <label class="optional">Post Code
                  </label>
                  <input name="mile" type="text" pattern="[0-9]"/>
                  <!--
                  <label class="obinfo">* obligatory fields-->
                  </label>
            </p>
          </fieldset>
<fieldset class="row2">
              <legend>Assignment in-Charge
</legend>
      <p>
            <label class="optional"> Booking Person Name if Different </label>
            <input type="text" class="long" name="name" pattern="[a-zA-Z][a-zA-Z ]{5,30}" required=''/>
        </p>
        <p>
            <label class="optional">Contact Number&nbsp;</label>
                    <input type="text" class="long" name="company"/>
                </p>
                <p>
                  <label class="optional"> Email Address&nbsp;</label>
                    <input type="text" name="phone2" pattern="[0-9]{5,30}" placeholder='' required=''/>
</p>
              <p>
                <label class="optional">Building Number / Name
                    </label>
                    <input type="text" name="phone" pattern="[0-9]{5,30}" placeholder='' required=''/>
                </p>
    <p>
                <label class="optional">Street / Road
      </label>
                    <input type="text" name="phone" pattern="[0-9]{5,30}" placeholder='' required=''/>
              </p>
              <p>
                <label class="optional">City
                </label>
                <select name="city">
                <option>--Select--</option>
                  <optgroup label="England">
                    <option>Bedfordshire</option>
                    <option>Berkshire</option>
                    <option>Bristol</option>
                    <option>Buckinghamshire</option>
                    <option>Cambridgeshire</option>
                    <option>Cheshire</option>
                    <option>City of London</option>
                    <option>Cornwall</option>
                    <option>Cumbria</option>
                    <option>Derbyshire</option>
                    <option>Devon</option>
                    <option>Dorset</option>
                    <option>Durham</option>
                    <option>East Riding of Yorkshire</option>
                    <option>East Sussex</option>
                    <option>Essex</option>
                    <option>Gloucestershire</option>
                    <option>Greater London</option>
                    <option>Greater Manchester</option>
                    <option>Hampshire</option>
                    <option>Herefordshire</option>
                    <option>Hertfordshire</option>
                    <option>Isle of Wight</option>
                    <option>Kent</option>
                    <option>Lancashire</option>
                    <option>Leicestershire</option>
                    <option>Lincolnshire</option>
                    <option>Merseyside</option>
                    <option>Norfolk</option>
                    <option>North Yorkshire</option>
                    <option>Northamptonshire</option>
                    <option>Northumberland</option>
                    <option>Nottinghamshire</option>
                    <option>Oxfordshire</option>
                    <option>Rutland</option>
                    <option>Shropshire</option>
                    <option>Somerset</option>
                    <option>South Yorkshire</option>
                    <option>Staffordshire</option>
                    <option>Suffolk</option>
                    <option>Surrey</option>
                    <option>Tyne and Wear</option>
                    <option>Warwickshire</option>
                    <option>West Midlands</option>
                    <option>West Sussex</option>
                    <option>West Yorkshire</option>
                    <option>Wiltshire</option>
                    <option>Worcestershire</option>
                  </optgroup>
                  <optgroup label="Scotland">
                    <option>Aberdeenshire</option>
                    <option>Angus</option>
                    <option>Argyllshire</option>
                    <option>Ayrshire</option>
                    <option>Banffshire</option>
                    <option>Berwickshire</option>
                    <option>Buteshire</option>
                    <option>Cromartyshire</option>
                    <option>Caithness</option>
                    <option>Clackmannanshire</option>
                    <option>Dumfriesshire</option>
                    <option>Dunbartonshire</option>
                    <option>East Lothian</option>
                    <option>Fife</option>
                    <option>Inverness-shire</option>
                    <option>Kincardineshire</option>
                    <option>Kinross</option>
                    <option>Kirkcudbrightshire</option>
                    <option>Lanarkshire</option>
                    <option>Midlothian</option>
                    <option>Morayshire</option>
                    <option>Nairnshire</option>
                    <option>Orkney</option>
                    <option>Peeblesshire</option>
                    <option>Perthshire</option>
                    <option>Renfrewshire</option>
                    <option>Ross-shire</option>
                    <option>Roxburghshire</option>
                    <option>Selkirkshire</option>
                    <option>Shetland</option>
                    <option>Stirlingshire</option>
                    <option>Sutherland</option>
                    <option>West Lothian</option>
                    <option>Wigtownshire</option>
                  </optgroup>
                  <optgroup label="Wales">
                    <option>Anglesey</option>
                    <option>Brecknockshire</option>
                    <option>Caernarfonshire</option>
                    <option>Carmarthenshire</option>
                    <option>Cardiganshire</option>
                    <option>Denbighshire</option>
                    <option>Flintshire</option>
                    <option>Glamorgan</option>
                    <option>Merioneth</option>
                    <option>Monmouthshire</option>
                    <option>Montgomeryshire</option>
                    <option>Pembrokeshire</option>
                    <option>Radnorshire</option>
                  </optgroup>
                  <optgroup label="Northern Ireland">
                    <option>Antrim</option>
                    <option>Armagh</option>
                    <option>Down</option>
                    <option>Fermanagh</option>
                    <option>Londonderry</option>
                    <option>Tyrone</option>
                  </optgroup>
                </select>
              </p>
            <p>
                  <label class="optional">Post Code 
           	  </label>
                  <input type="text" name="officeph" maxlength="15" pattern="[0-9]{11}"/>
              </p>
</fieldset>
            <fieldset class="row3">
                <legend>Booking Organisation Details
                </legend>
                <p>
                  <label> Company Name* </label>
                  <input type="text" name="phone3" pattern="[0-9]{5,30}" placeholder='' required=''/>
                </p>
                <p>
                  <label class="optional">Booking Ref </label>
                  <input type="text" name="phone4" pattern="[0-9]{5,30}" placeholder='' required=''/>
                </p>
                <!--<p>
                    <label>Birthdate *
                    </label>
                    <select class="date">
                        <option value="1">01
                        </option>
                        <option value="2">02
                        </option>
                        <option value="3">03
                        </option>
                        <option value="4">04
                        </option>
                        <option value="5">05
                        </option>
                        <option value="6">06
                        </option>
                        <option value="7">07
                        </option>
                        <option value="8">08
                        </option>
                        <option value="9">09
                        </option>
                        <option value="10">10
                        </option>
                        <option value="11">11
                        </option>
                        <option value="12">12
                        </option>
                        <option value="13">13
                        </option>
                        <option value="14">14
                        </option>
                        <option value="15">15
                        </option>
                        <option value="16">16
                        </option>
                        <option value="17">17
                        </option>
                        <option value="18">18
                        </option>
                        <option value="19">19
                        </option>
                        <option value="20">20
                        </option>
                        <option value="21">21
                        </option>
                        <option value="22">22
                        </option>
                        <option value="23">23
                        </option>
                        <option value="24">24
                        </option>
                        <option value="25">25
                        </option>
                        <option value="26">26
                        </option>
                        <option value="27">27
                        </option>
                        <option value="28">28
                        </option>
                        <option value="29">29
                        </option>
                        <option value="30">30
                        </option>
                        <option value="31">31
                        </option>
                    </select>
                    <select>
                        <option value="1">January
                        </option>
                        <option value="2">February
                        </option>
                        <option value="3">March
                        </option>
                        <option value="4">April
                        </option>
                        <option value="5">May
                        </option>
                        <option value="6">June
                        </option>
                        <option value="7">July
                        </option>
                        <option value="8">August
                        </option>
                        <option value="9">September
                        </option>
                        <option value="10">October
                        </option>
                        <option value="11">November
                        </option>
                        <option value="12">December
                        </option>
                    </select>
                    <input class="year" type="text" size="4" maxlength="4"/>e.g 1976
                </p>-->
                <p>
                  <label>Contact Name&nbsp;* </label>
                  <input type="text" name="phone5" pattern="[0-9]{5,30}" placeholder='' required=''/>
                </p>
                <div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
                  <p>
                    <textarea name="remrks" cols="51" rows="5"></textarea>
                  </p>
              </div>
            </fieldset>
            <fieldset class="row4">
                <legend>Interpreter
                </legend>
                <p>
                  
                  <label class="optional">Gender</label>
                  <input type="radio" name="gender" value="radio"/>
                  <label class="gender">Male</label>
                  <input type="radio" name="gender" value="radio"/>
                  <label class="gender">Female</label>
                </p>
                
            </fieldset>
            <div><button class="button">Submit &raquo;</button></div>
        </form>
</body>
</html>





