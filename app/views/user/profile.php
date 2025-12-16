<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5">
      <div>
        <h3 class="h3 mb-0">Profile</h3>
      </div>
    </div>
    
    <div class="card">
      <!--img-->
      <div class="card-body p-md-5">
        <!-- Cover -->
        <div class="position-relative mb-4" 
             style="background-image: url('<?= $coverUrl?>'); background-position: center; background-size: cover; background-repeat: no-repeat; height: 228px">
            
            <a href="javascript:void(0)" 
               onclick="document.getElementById('cover_input').click()" 
               class="position-absolute bottom-0 end-0 btn btn-light m-3 shadow">
                <i class="bi bi-camera"></i> Changer le cover
            </a>
            
            <form method="POST" action="/profile/upload" enctype="multipart/form-data" id="cover_form" class="d-none">
                <input type="hidden" name="type" value="cover">
                <input type="file" name="cover" id="cover_input" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>
        
        <div class="text-center position-relative d-inline-block" style="margin-top: 880px; bottom:950px;">
            <img src="<?= $avatarUrl ?>" 
                 alt="Avatar" 
                 class="rounded-circle border border-5 border-white shadow" 
                 width="200" height="200">
            
            <a href="javascript:void(0)" 
               onclick="document.getElementById('avatar_input').click()" 
               class="position-absolute bottom-0 end-0 btn btn-primary rounded-circle p-2 shadow">
                <i class="bi bi-camera-fill"></i>
            </a>
            
            <form method="POST" action="/profile/upload" enctype="multipart/form-data" id="avatar_form" class="d-none">
                <input type="hidden" name="type" value="avatar">
                <input type="file" name="avatar" id="avatar_input" accept="image/*" onchange="this.form.submit()">
            </form>
        </div>
        
        <div class="d-flex flex-column gap-5 position-relative" style="bottom: 900px;">
          <div class="d-flex flex-column gap-3">
            <div class="d-flex flex-md-row flex-column justify-content-between gap-2">
              <!--heading-->
              <div>
                <h1 class="mb-0"><?= $nom?></h1>
                <!--content-->
                <div class="d-flex flex-lg-row flex-column gap-2">
                  <small class="fw-medium text-gray-800"><?= $jobTitle?></small>
                  <small class="fw-medium text-success"><?= $experienceDetails?></small>
                </div>
              </div>
              <!--button-->
              <div class="d-flex flex-row gap-3 align-items-center">
                <a href="messagerie" class="btn btn-outline-white">
                  <span>
                                        <i class="bi bi-chat-dots"></i>

                  </span>
                  Messagerie
                </a>
                <a href="/settings" class="btn btn-outline-white">Modifier</a>
              </div>
            </div>
            
            <div class="d-flex flex-md-row flex-column gap-md-4 gap-2">
              <div class="d-flex flex-row gap-2 align-items-center lh-1">
                <!--icon-->
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-star-fill text-warning align-baseline" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
                  </svg>
                </span>
                <span>
                  <!--text-->
                  <span class="text-gray-800 fw-bold">5.0</span>
                  (16&nbsp;Skills)
                </span>
              </div>
              
              <div class="d-flex flex-row gap-2 align-items-center lh-1">
                <!--icon-->
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-people-fill text-primary align-baseline" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"></path>
                  </svg>
                </span>
                <!--text-->
                <span>
                  <span class="text-gray-800 fw-bold">+</span>
                  depelacement
                </span>
              </div>
              
              <div class="d-flex flex-row gap-2 align-items-center lh-1">
                <!--icon-->
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-geo-alt-fill text-danger" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"></path>
                  </svg>
                </span>
                <!--text-->
                <span>employee</span>
              </div>
            </div>
          </div>
          
          <div class="d-flex flex-column gap-2">
            <!--heading-->
            <h3 class="mb-0">Skills</h3>
            <div class="gap-2 d-flex flex-wrap">
              <a href="#!" class="btn btn-tag btn-sm">Frontend</a>
              <a href="#!" class="btn btn-tag btn-sm">HTML</a>
              <a href="#!" class="btn btn-tag btn-sm">CSS</a>
              <a href="#!" class="btn btn-tag btn-sm">React</a>
              <a href="#!" class="btn btn-tag btn-sm">Javascript</a>
              <a href="#!" class="btn btn-tag btn-sm">Vuejs</a>
              <a href="#!" class="btn btn-tag btn-sm">Next.js</a>
            </div>
          </div>
          
          <div>
            <span class="badge rounded-pill text-success-emphasis bg-success-subtle border border-success align-items-center">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-reply-fill me-1 align-text-top" viewBox="0 0 16 16">
                  <path d="M5.921 11.9 1.353 8.62a.72.72 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"></path>
                </svg>
              </span>
              Quick Responder
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>