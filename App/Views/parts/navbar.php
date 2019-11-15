<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="/">Books</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <?php foreach($categories as $category): ?>
      <li class="nav-item active">
        <a class="nav-link" href="/genre/<?= lcfirst($category['name_en']); ?>"><?= $category['name_' . $lang]; ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
    <ul class="navbar-nav ml-auto">
    <li class="nav-item <?=$_SESSION['lang']=='az'?'active':'';?>"><a class="nav-link" href="?lang=az">az</a></li>
    <li class="nav-item <?=$_SESSION['lang']=='en'?'active':'';?>"><a class="nav-link" href="?lang=en">en</a></li>
    <li class="nav-item <?=$_SESSION['lang']=='ru'?'active':'';?>"><a class="nav-link" href="?lang=ru">ru</a></li>
  	</ul>
    <form class="form-inline my-2 my-lg-0 ml-5" action="/search" method="post">
      <input class="form-control mr-sm-2" type="search" name="search" placeholder="<?=$searchButton?>" aria-label="Search" required>
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><?=$searchButton?></button>
    </form>
  </div>
</nav>