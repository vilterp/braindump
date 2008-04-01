<html>
  <head>
    <title>SQL Console</title>
  </head>
  <body>
    <h1>SQL Console</h1>
    <hr>
  </body>
  <form action='' method='post'>
    <textarea name="query" rows="8" cols="40"><?php echo $_POST['query'] ?></textarea><br />
    <input type="submit" value="Run &raquo;" accesskey="r">
  </form>
  <?php
  include 'app/config.php'; // what if this changes... it would be good to grab the constants from index.php
  if(!empty($_POST['query'])) {
    $db = sqlite_open($config['database']);
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
</html>