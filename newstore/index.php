<?php include 'routes.php'; ?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>E-STORE</title>
  <?php include 'inc-meta.php'; ?>
  <link href="css/template-e-store.css?v=<?php echo time(); ?>" rel="stylesheet">
  <?php include 'inc-cdn.php'; ?>
</head>

<body>

  <?php include 'template/head-bar.php'; ?>
  <main>
    <div id="sections_root_store"></div>
  </main>
  <?php include 'template/footer-bar.php'; ?>

  <script type="module">
    import(`${pathConfig.BASE_WEB}js/storeRender.js?v=<?= time() ?>`)
      .then(async ({ 
        fetchIndexData, 
        renderSections, 
        renderIntroduce, 
        renderBanners, 
        renderCarouselSM, 
        renderCarouselMD, 
        renderCarouselLG 
      }) => {

        const service = pathConfig.BASE_WEB + 'service/index-data.php?';
        const sections = await fetchIndexData("getSectionItems", service);
        sections.data.sort((a, b) => a.sort - b.sort);
        renderSections("#sections_root_store", sections.data);

        for (const section of sections.data) {
            switch (section.type) {
              case "crssm":
                const popularItems = await fetchIndexData("getPopularItems", service);
                renderCarouselSM("#" + section.carouselId, popularItems.data);
                break;
              case "crsmd":
                const productItems = await fetchIndexData("getProductItems", service);
                renderCarouselMD("#" + section.carouselId, productItems.data);
                break;
              case "crslg":
                const newsItems = await fetchIndexData("getNewsItems", service);
                renderCarouselLG("#" + section.carouselId, newsItems.data);
                break;
              case "bbn":
                const banners = await fetchIndexData("getBannersItems", service);
                renderBanners("#" + section.carouselId, banners.data);
                break;
              case "intd":
                const introItems = await fetchIndexData("getIntroItems", service);
                renderIntroduce("#" + section.carouselId, introItems.data);
                break;
            }
        }
        
      })
      .catch((e) => console.error("Module import failed", e));
  </script>

</body>

</html>