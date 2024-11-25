<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
   <div class="container-fluid">
       <div class="logo-box"><div class="logo"></div></div>
      <a class="navbar-brand" href="#">Express Convert IO</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarColor02">
         <ul class="navbar-nav ms-auto">
            <li class="nav-item">
               <a class="nav-link active" href="#">Accueil
                  <span class="visually-hidden">(current)</span>
               </a>
            </li>
            <li class="nav-item" aria-label="Convertir">
               <a class="nav-link" href="<?= base_url('/') ?>">Convertir</a>
            </li>
            <li class="nav-item" aria-label="Redimensionner">
               <a class="nav-link" href="<?= base_url('/scale') ?>">Redimensionner</a>
            </li>
            <li class="nav-item" aria-label="Améliorer">
               <a class="nav-link" href="#">Améliorer</a>
            </li>
            <li class="nav-item" aria-label="Supprimer fond">
               <a class="nav-link" href="#">Supprimer fond</a>
            </li>

         </ul>
<!--         <form class="d-flex">-->
<!--            <input class="form-control me-sm-2" type="search" placeholder="Search">-->
<!--            <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>-->
<!--         </form>-->
      </div>
   </div>
</nav>