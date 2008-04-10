<html>
  <head>
    <title>SQL Console</title>
  </head>
  <body onload="document.getElementById('querybox').focus()">
    <h1>SQL Console</h1>
    <hr>
    <form action='' method='post'>
      <textarea id="querybox" name="query" rows="8" cols="40"><?php echo $_POST['query'] ?></textarea><br />
      <input type="submit" value="Run &raquo;" accesskey="r">
    </form>
    <?php
    include 'lib/spyc.php';
    $config = Spyc::YAMLLoad('app/config.yaml');
    if(!empty($_POST['query'])) {
      $db = sqlite_open($config['database']['path']);
      $result = sqlite_fetch_all(sqlite_query($db,$_POST['query']),SQLITE_ASSOC);
      if(count($result) > 0) {
        $keys = array_keys($result[0]); ?>
        <table border='1'>
          <tr>
            <?php foreach ($keys as $key): ?>
              <th><?php echo $key ?></th>
            <?php endforeach ?>
          </tr>
          <?php foreach ($result as $row): ?>
            <tr>
              <?php foreach ($row as $cell): ?>
                <td><?php echo $cell ?></td>
              <?php endforeach ?>
            </tr>
          <?php endforeach ?>
        </table>
      <?php }
    } else {
      echo "<p>no result.</p>";
    }
    ?>
  </body>
</html>