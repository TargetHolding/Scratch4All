<?php
require 'ingest_include.php';
list($bookdir, $filename, $extension) = bfe_parameters();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
  <head>
    <title>Ingest Fields</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel='stylesheet' type='text/css' href='../include/ingest_basic_style.css'/>
    <style type="text/css">
      #div_popup_overlay
      {
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        position: fixed;
        z-index: 1;
        background-color: rgb(211,211,211); /*Using RGB for floating div: bug workaround IE7 */
        display: none;
        filter: alpha(opacity=50);
        opacity: 0.5;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
      }
      #div_popup
      {
        position: fixed;
        z-index: 2;
        background-color: rgb(211,211,211); /*Using RGB for floating div: bug workaround IE7 */
        display: none;
        border: solid;
        overflow: auto;
        top: 25px;
        left: 300px;
        right: 25px;
        bottom: 25px;
      }
      #div_close
      {
        position: fixed;
        color: blue;
        cursor: pointer;
        text-decoration: underline;
        font-weight: bold;
        top: 5px;
        left: 300px;
      }
      table, th, tr, td
      {
        border-width: 1px;
        background-color: white;
        border-collapse: collapse;
        border-style: inset;
        border-color: gray;
      }
    </style>
  </head>
  <body>
    <div id='div_popup'><!----></div>
    <div id='div_popup_overlay' onClick='javascript:close_popup()'><div id="div_close" onClick="close_popup()">close</div></div>
    <div id='div_fields' style='width: 300px; float: left;'><!----></div>
    <div id='div_diagnostics' style='position: absolute; left: 300px; width: 70%; text-align: center;'><!----></div>
    <div id='div_poll_time' style='width:300px'><!----></div>
    <script type='text/javascript'>
      var ingest_bash = 'ingest_bash.php';
      var ingest_logfile = 'ingest_logfile.php';
      var pd_interval = 2;
      var pd_timeout;
      var pd_start_date;
      var rsp_timeout;
      var rsp_timer;
      var rsp_timer_countdown_value = 60;
      var color_version = 'web';
      var samples='';
      var bookdir = '<?php print $bookdir; ?>';
      var filename = '<?php print $filename; ?>';
      var extension = '<?php print $extension; ?>';
      var done_file = '<?php print sprintf($done_file_format, $bookdir, $filename); ?>';
      var diagnostics_url_format = '<?php print sprintf($diagnostics_url_format, $bookdir, '%filename'); ?>';
      var orginal_page_format = '<?php print sprintf($orginal_page_format, $bookdir, '%filename'); ?>';
      var ingest_param_lines ='<?php print str_replace("\"", "", str_replace("\r", "", str_replace("\n", "&", file_get_contents(ingest_params)))); ?>'.split('&');
      var ingest_params = [];
      for(var key in ingest_param_lines)
      {
        line = ingest_param_lines[key].split('\t');
        if (line[0])
          ingest_params[key]=[line[0],line[1],line[2]];
      }
      make_hmtl_fields();
      bash = http_request(make_query(ingest_bash, [['bookdir', bookdir],['filename', filename],['extension', extension]]));
      process_bash(bash);
      update_offset();
      diagnostics();

      function make_hmtl_fields()
      {
        var html  = '<h1 style="margin:0px">Ingest fields</h1>';
        html     += '<form>';
        for(var key in ingest_params)
        {
          if (ingest_params[key][0])
          {
            html += '<label style="float:left; width:150px; cursor:pointer;" title="'+ingest_params[key][2]+'" for="' + ingest_params[key][0] + '">'+ingest_params[key][0]+'</label>'
            html += '<input type="text" style="float:left; width:20px; background-color:red; cursor:help;" tabIndex="-1" id="colorbox_'+ingest_params[key][0]+'" onclick="javascript:show_help()" readonly="readonly">';
            html += '<input type="text" style="float:left; width:100px;" id="'+ingest_params[key][0]+'" value="'+ingest_params[key][1]+'" onchange="javascript:change_color(\'colorbox_'+ingest_params[key][0]+'\')" oninput="javascript:change_color(\'colorbox_'+ingest_params[key][0]+'\')" />';
          }
        }
        html +=  '</form>';
        html +=  '<div>';
        html +=    '<button style="width:280px;" onclick="javascript:commit()">Commit</button>';
        html +=  '</div>';
        html +=  '<div>';
        html +=    '<button style="width:140px;" onclick="javascript:diagnostics()">Refresh</button>';
        html +=    '<button style="width:140px;" onclick="javascript:defaults()">Default values</button>';
        html +=  '</div>';
        html +=  '<div>';
        html +=    '<button style="width:140px;" onclick="javascript:toggleversion()" id="versionbut">Show in color</button>';
        html +=    '<form action="ingest_file.php" style="display:inline;"><input type="hidden" name="path" value="/' + bookdir + '"><input type="submit" style="width:140px;" value="Ingest other page"></form>'
        html +=  '</div>';
        html +=  '<div>';
        html +=    '<button style="width:140px;" onClick="javascript:rsp()">Random Sample</button>';
        html +=    '<button style="width:140px;" onClick="javascript:log()">Show log</button>';
        html +=  '</div>';
        document.getElementById('div_fields').innerHTML = html;
      }

      function rsp()
      {
        samples = document.getElementById('RANDOM_SAMPLE').value.split(' ');
        var html = '';
        html += '<h1>Random sample</h1>';
        html += '<table style="margin:2%;">';
        html +=   '<tr>';
        html +=     '<td style="cursor:pointer; color:blue;" onclick="new_random_sample()">Create new random sample</td>';
        html +=     '<td id="td_srs_update" colspan="4" style="cursor:pointer; color:blue;" onClick="rsp_update_links()"><center>Update</center></td>';
        html +=     '<td></td>';
        html +=   '</tr>';
        html +=   '<tr>';
        html +=     '<th title="Random sample is <book_id> + _ + <page>">Random sample</th>'
        html +=     '<th title="Show cut lines in black and white. Link is available when '+ done_file +' is present">Lines bw</th>'
        html +=     '<th title="Show cut lines in color. Link is available when '+ done_file +' is present">Lines rgb</th>'
        html +=     '<th title="Show diagnostics. Link is available when '+ done_file +' is present">Diagnostics</th>'
        html +=     '<th title="Show orginal page. Link available when page jpg image is present.">Orginal page</th>'
        html +=     '<th title="Cumpute random sample(s)">Compute</th>';
        html +=   '</tr>';
        html +=   '<tr>';
        html +=     '<td></td>'
        html +=     '<td style="cursor:pointer; color:blue; font-weight:bold;" onClick="rsp_show_lines(\'web\')">open all</td>'
        html +=     '<td style="cursor:pointer; color:blue; font-weight:bold;" onClick="rsp_show_lines(\'web-grey\')">open all</td>'
        html +=     '<td style="cursor:pointer; color:blue; font-weight:bold;" onClick="rsp_show_diagnostics()">open all</td>'
        html +=     '<td style="cursor:pointer; color:blue; font-weight:bold;" onClick="rsp_show_pages()">open all</td>'
        html +=     '<td style="cursor:pointer; color:blue; font-weight:bold;" onClick="rsp_compute_requests()">(re)compute all</td>';
        html +=   '</tr>';
        for (var key in samples)
        {
          var sample = samples[key];
          html += '<tr>';
          html +=   '<td>' + sample + '</td>';
          html +=   '<td id="td_rsp_show_web_lines_' + sample + '"></td>';
          html +=   '<td id="td_rsp_show_webgrey_lines_' + sample + '"></td>';
          html +=   '<td id="td_rsp_show_diagnostics_' + sample + '"></td>';
          html +=   '<td id="td_rsp_show_page_' + sample + '"></td>';
          html +=   '<td id="td_rsp_compute_request_' + sample + '"><div style="cursor:pointer; color:blue;" onClick="rsp_compute_request(\'' + sample + '\')">compute</div></td>';
          html += '</tr>';
        }
        html += '</table>';
        html += '<button style="margin-left:2%; width:280px;" onClick="rsp()">Reload</button><br>';
        html += '<div id="divTimer"></div>';
        document.getElementById('div_popup').innerHTML = html;
        popup();
        rsp_timer = 0;
        rsp_auto_update_links()
      }

      function rsp_auto_update_links()
      {
        try
        {
          document.getElementById('td_srs_update').innerHTML = '<center> Update (' + rsp_timer + 's)</center>';
          if (rsp_timer <= 0)
          {
            rsp_timer = rsp_timer_countdown_value;
            rsp_update_links();
          }
          else
            rsp_timer--;
          clearTimeout(rsp_timeout);
          rsp_timeout = setTimeout(rsp_auto_update_links, 1000);
        }
        catch(err)
        {
          clearTimeout(rsp_timeout);
        }
      }

      function rsp_update_links()
      {
        rsp_timer = rsp_timer_countdown_value;
        for (var key in samples)
        {
          var sample = samples[key];
          rsp_update_page_link(sample);
          var poll_done = http_request(make_query('poll_done.php', [['bookdir', bookdir],['filename', sample],['extension', extension]]));
          if (poll_done == '0')
          {
            try {document.getElementById('td_rsp_show_web_lines_' + sample).innerHTML = '<div>unavailable</div>';} catch(err){};
            try {document.getElementById('td_rsp_show_webgrey_lines_' + sample).innerHTML = '<div>unavailable</div>';} catch(err){};
            try {document.getElementById('td_rsp_show_diagnostics_' + sample).innerHTML = '<div>unavailable</div>';} catch(err){};
          }
          if (poll_done == '1')
          {
            try {document.getElementById('td_rsp_compute_request_' + sample).innerHTML = '<div style="cursor:pointer; color:blue;" onClick="rsp_compute_request(\'' + sample + '\')">recompute</div>';} catch(err){};
            var lines_web_url = make_query('ingest_get_lines.php', [['bookdir', bookdir],['filename', sample],['extension', extension],['version', 'web']]);
            try {document.getElementById('td_rsp_show_web_lines_' + sample).innerHTML = '<div style="cursor:pointer; color:blue;" onClick="window.open(\'' + lines_web_url + '\')">link</div>';} catch(err){};
            var lines_webgrey_url = make_query('ingest_get_lines.php', [['bookdir', bookdir],['filename', sample],['extension', extension],['version', 'web-grey']]);
            try {document.getElementById('td_rsp_show_webgrey_lines_' + sample).innerHTML = '<div style="cursor:pointer; color:blue;" onClick="window.open(\'' + lines_webgrey_url + '\')">link</div>';} catch(err){};
            var diagnostics_url = diagnostics_url_format.replace('%filename', sample);
            try {document.getElementById('td_rsp_show_diagnostics_' + sample).innerHTML = '<div style="cursor:pointer; color:blue;" onClick="window.open(\'' + diagnostics_url + '\')">link</div>';} catch(err){};
          }
        }
      }

      function rsp_show_lines(color_version)
      {
        for (var key in samples)
        {
          var sample = samples[key];
          rsp_show_line(sample, color_version)
        }
      }

      function rsp_show_line(sample, color_version)
      {
        var poll_done = http_request(make_query('poll_done.php', [['bookdir', bookdir],['filename', sample],['extension', extension]]));
        if (poll_done == '1')
        {
          window.open(make_query('ingest_get_lines.php', [['bookdir', bookdir],['filename', sample],['extension', extension],['version', color_version]]));
        }
      }

      function rsp_show_diagnostics()
      {
        for (var key in samples)
        {
          var sample = samples[key];
          rsp_show_diagnostic(sample)
        }
      }

      function rsp_show_diagnostic(sample)
      {
        var poll_done = http_request(make_query('poll_done.php', [['bookdir', bookdir],['filename', sample],['extension', extension]]));
        if (poll_done == '1')
        {
          var diagnostics_url = diagnostics_url_format.replace('%filename', sample);
          window.open(diagnostics_url);
        }
      }

      function rsp_show_pages()
      {
        for (var key in samples)
        {
          var sample = samples[key];
          rsp_show_page(sample)
        }
      }

      function rsp_show_page(sample)
      {
        var page_url = orginal_page_format.replace('%filename', sample);
        var page_image = new Image();
        page_image.src = page_url;
        page_image.onload = function(){window.open(this.src);}
      }
      
      function rsp_update_page_link(sample)
      {
        try {document.getElementById('td_rsp_show_page_' + sample).innerHTML = '<div>unavailable</div>';} catch(err){};
        var page_url = orginal_page_format.replace('%filename', sample);
        var page_image = new Image();
        page_image.sample = sample;
        page_image.src = page_url;
        page_image.onload = function(){try{document.getElementById('td_rsp_show_page_' + this.sample).innerHTML = '<div style="cursor:pointer; color:blue;" onClick="window.open(\'' + this.src + '\')">link</div>';}catch(err){}}
      }

      function rsp_compute_requests()
      {
        for (var key in samples)
        {
          var sample = samples[key];
          rsp_compute_request(sample);
        }
      }

      function rsp_compute_request(sample)
      {
        var bash = http_request(make_query(ingest_bash, [['bookdir', bookdir],['filename', sample],['extension', extension],['compute_request',true]]));
        if (bash.match('ERROR:'))
          document.getElementById('div_popup').innerHTML += '<br>' + bash;
        else
        {
          document.getElementById('td_rsp_compute_request_' + sample).innerHTML = '<div>computing</div>';
          document.getElementById('td_rsp_show_web_lines_' + sample).innerHTML = '<div>unavailable</div>';
          document.getElementById('td_rsp_show_webgrey_lines_' + sample).innerHTML = '<div>unavailable</div>';
          document.getElementById('td_rsp_show_diagnostics_' + sample).innerHTML = '<div>unavailable</div>';
        }
      }

      function new_random_sample()
      {
        var x = http_request(make_query(ingest_bash, [['bookdir', bookdir],['filename', filename],['extension', extension], ['new_random_sample',true]]));
        process_bash(x);
        rsp();
      }


      function log()
      {
        var url = make_query(ingest_logfile, [['bookdir', bookdir],['filename', filename],['extension', extension]]);
        var log = http_request(url).replace(/['"]/g,'');
        if (bash.match('ERROR:'))
          alert(bash);
        var log_lines = log.split('\n');
        var parts = new Array();
        var datetime = '';
        var log_table = '';
        log_table += '<h1>Log file</h1>';
        log_table += '<fieldset>';
        log_table += '<legend>Help</legend>';
        log_table += 'Exit: press outside log display<br>';
        log_table += 'Restore value: press on value <br>';
        log_table += 'Restore group: press on date <br>';
        log_table += '</fieldset>';
        log_table += '<table>';
        for (var key in log_lines)
        {
          parts = log_lines[key].split('\t');
          if (parts[0])
          {
            if (parts[0] != datetime)
            {
              log_table += '</table><table style="width:96%; margin:2%;">';
              log_table += '<tr>'
              log_table +=   '<th style="width:20%; color:blue; cursor:pointer;" onClick="javascript:log_set_group(\''+parts[0]+'\')">'+parts[0]+'</th>'
              log_table +=   '<th style="width:40%;">Form</th>'
              log_table +=   '<th style="width:40%;">To</th>'
              log_table += '</tr>';
            }
            datetime = parts[0];
            log_table += '<tr>'
            log_table +=   '<td>'+parts[1]+'</td>'
            log_table +=   '<td style="cursor:pointer; color:blue;" onClick="javascript:log_set_param(\''+parts[1]+'\',\''+parts[2]+'\')">'+parts[2]+'</td>'
            log_table +=   '<td style="cursor:pointer; color:blue;" onClick="javascript:log_set_param(\''+parts[1]+'\',\''+parts[3]+'\')">'+parts[3]+'</td>'
            log_table += '</tr>';
          }
        }
        log_table += '</table>';
        document.getElementById('div_popup').innerHTML = log_table;
        popup();
      }

      function log_set_param(param, value)
      {
        if (document.getElementById(param).value.toString() != value.toString())
        {
          document.getElementById(param).value = value;
          document.getElementById('colorbox_'+param).style.background = 'green';
        }
      }

      function log_set_group(date)
      {
        var url = make_query(ingest_logfile, [['bookdir', bookdir],['filename', filename],['extension', extension]]);
        var log = http_request(url).replace(/['"]/g,'');
        var log_lines = log.split('\n');
        var parts = new Array();
        for (var key in log_lines)
        {
          parts = log_lines[key].split('\t');
          if (parts[0] == date)
          {
            if (document.getElementById(parts[1]).value.toString() != parts[2].toString())
            {
              document.getElementById(parts[1]).value = parts[2];
              document.getElementById('colorbox_'+parts[1]).style.background = 'green';
            }
          }
        }
      }

      function http_request(url)
      {
        if (window.XMLHttpRequest)
          xmlhttp=new XMLHttpRequest();
        else
          xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
        xmlhttp.open('GET', url, false);
        xmlhttp.send();
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
          return xmlhttp.responseText;
        else
          alert('xmlhttp error\nreadyState: ' + xmlhttp.readyState + '\nstatus: ' + xmlhttp.status + '\nrequested URL: ' + url);
      }

      function defaults()
      {
        for(var key in ingest_params)
        {
          try
          {
            if (!ingest_params[key][0].match('t_soft'))
            {
              if (document.getElementById(ingest_params[key][0]).value != ingest_params[key][1].replace(/['"]/g,''))
              {
                document.getElementById(ingest_params[key][0]).value = ingest_params[key][1].replace(/['"]/g,'');
                document.getElementById('colorbox_'+ingest_params[key][0]).style.background = 'red';
              }
            }
          }
          catch(err){};
        }
      }

      function process_bash(bash)
      {
        if (bash.match('ERROR:'))
          alert(bash);
        var bashlines = bash.split('\n');
        var line;
        for(var key in bashlines)
        {
          if (bashlines[key])
          {
            line = bashlines[key].split('=');
            try
            {
              document.getElementById(line[0]).value = line[1].replace(/['"]/g,'');
              document.getElementById('colorbox_'+line[0]).style.background = 'black';
            }
            catch(err)
            {
            }
            if (bashlines[key].match('WARNING:'))
              alert(bashlines[key]);
          }
        }
      }

      function change_color(id)
      {
        document.getElementById(id).style.background = 'green';
      }

      function poll_done()
      {
        // Update poll-feedback
        var d = new Date();
        var diff = 0;
        if (pd_start_date)
          diff = (d - pd_start_date) / 1000;
        document.getElementById('div_poll_time').innerHTML = 'Time polling: '+Math.round(diff)+'s';
        var done = http_request(make_query('poll_done.php', [['bookdir', bookdir],['filename', filename],['extension', extension]]));
        if (done == '1')
        {
          diagnostics();
          document.getElementById('div_poll_time').innerHTML = '<span style="color: green;">Done polling. Reloaded diagnostics<br>Been polling for '+Math.round(diff)+' seconds</span>';
          return;
        }
        clearTimeout(pd_timeout);
        pd_timeout = setTimeout(poll_done, pd_interval * 1000);
      }

      function commit()
      {
        url = make_custom_query();
        if (!url)
          return;
        bash = http_request(url)
        process_bash(bash);
        pd_start_date = new Date();
        poll_done();
      }

      function make_custom_query()
      {
        var url = make_query(ingest_bash, [['bookdir', bookdir],['filename', filename],['extension', extension],['compute_request',true]]);
        for(var key in ingest_params)
        {
          // quick check whether nfilt is an odd number
          if (ingest_params[key][0] == 'nfilt')
          {
            var val = document.getElementById(ingest_params[key][0]).value ;
            if (parseInt(val) % 2 == 0)
            {
              alert("nfilt (now: " + val + ") should be odd!");
              return false;
            }
          }
          url = make_query(url, [[ingest_params[key][0], document.getElementById(ingest_params[key][0]).value]]);
        }
        return url;
      }

      function make_query(page, pvs)
      {
        var url = page;
        for(var key in pvs)
        {
          if (!url.match('[?]'))
            url += '?'+pvs[key][0]+'='+pvs[key][1];
          else
            url += '&'+pvs[key][0]+'='+pvs[key][1];
        }
        return url;
      }

      function diagnostics()
      {
        html  = '<h1 style="margin:0px">Diagnostics</h1>';
        html += '<a href="' + orginal_page_format.replace('%filename', filename) + '" target="_blank">Original image</a> | ';
        html += '<a href="' + diagnostics_url_format.replace('%filename', filename) + '" target="_blank">Full diagnostics page</a><br>'
        html += http_request(make_query('ingest_get_lines.php', [['bookdir', bookdir],['filename', filename],['extension', extension],['version', color_version]]));
        document.getElementById('div_diagnostics').innerHTML = html;
      }

      function toggleversion()
      {
        if (color_version == 'web')
        {
          color_version = 'web-grey';
          document.getElementById('versionbut').innerHTML = 'Show in b/w';
        }
        else
        {
          color_version = 'web';
          document.getElementById('versionbut').innerHTML = 'Show in color';
        }
        diagnostics();
      }

      function popup()
      {
        document.getElementById('div_popup').style.display = 'inline';
        document.getElementById('div_popup_overlay').style.display = 'inline';
      }

      function close_popup()
      {
        document.getElementById('div_popup').style.display = 'none';
        document.getElementById('div_popup_overlay').style.display = 'none';
        document.getElementById('div_popup').innerHTML = '';
      }

      function show_help()
      {
        var html  = '<fieldset style="margin-right:21px; margin-top:10px; margin-bottom:10px;">';
        html     +=   '<legend>Legend</legend>';
        html     +=   '<div><i><label style="float:left; width:150px;">Default value</label><input type="text" style="width:20px; background-color:red;" disabled="disabled"></i></div>';
        html     +=   '<div><i><label style="float:left; width:150px;">Entered value</label><input type="text" style="width:20px; background-color:green;" disabled="disabled"></i></div>';
        html     +=   '<div><i><label style="float:left; width:150px;">Saved value</label><input type="text" style="width:20px; background-color:black;" disabled="disabled"></i></div>';
        html     += '</fieldset>';
        document.getElementById('div_popup').innerHTML = html;
        popup();
      }

      function update_offset()
      {
<?php
$page = $_GET['page'];
$x1 = $_GET['dimX'];
$y1 = $_GET['dimY'];
$w = $_GET['dimW'];
$h = $_GET['dimH'];
$resx = $_GET['resX'];
$resy = $_GET['resY'];
if (strlen($page) && strlen($x1) && strlen($y1) && strlen($w) && strlen($h) && strlen($resx) && strlen($resy))
{
  $x2 = $resx - ($w + $x1);
  $y2 = $resy - ($h + $y1);
  $html = 'document.getElementById("' . $page . '_softleft"  ).value = ' . (int) $x1 . ';' . "\n";
  $html .= 'document.getElementById("' . $page . '_softtop"   ).value = ' . (int) $y1 . ';' . "\n";
  $html .= 'document.getElementById("' . $page . '_softright" ).value = ' . (int) $x2 . ';' . "\n";
  $html .= 'document.getElementById("' . $page . '_softbottom").value = ' . (int) $y2 . ';' . "\n";
  $html .= 'document.getElementById("colorbox_' . $page . '_softleft"  ).style.background = "green";' . "\n";
  $html .= 'document.getElementById("colorbox_' . $page . '_softtop"   ).style.background = "green";' . "\n";
  $html .= 'document.getElementById("colorbox_' . $page . '_softright" ).style.background = "green";' . "\n";
  $html .= 'document.getElementById("colorbox_' . $page . '_softbottom").style.background = "green";' . "\n";
  print $html;
}
?>
  }
    </script>
  </body>
</html>
