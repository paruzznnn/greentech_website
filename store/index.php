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
    <div id="sections_root_store">
      <div class="container">
          <div id="mainContent"></div>
      </div>
    </div>
  </main>
  <?php include 'template/footer-bar.php'; ?>

  <script type="module">
  import(`${window.AppConfig.BASE_WEB}js/storeRender.js?v=<?= time() ?>`)
    .then(async (storeRenderModule) => {

      const {
        fetchIndexData,
        renderSections,
        renderIntroduce,
        renderBanners,
        renderCarouselSM,
        renderCarouselMD,
        renderCarouselLG,
        renderGridCardSM,
        renderGridCardMD,
      } = storeRenderModule;

      const service = window.AppConfig.BASE_WEB + 'service/index-data.php?';

      // ดึง sections และ sort ก่อน render
      const sections = await fetchIndexData("getSectionItems", service);
      sections.data.sort((a, b) => a.sort - b.sort);
      renderSections("#mainContent", sections.data);

      // เก็บ mapping renderer
      const renderMap = {
        crssm: (selector, data) => renderCarouselSM(selector, data.data),
        crsmd: (selector, data) => renderCarouselMD(selector, data.data, {
          BASE_WEB: window.AppConfig.BASE_WEB,
          user: data.member
        }),
        crslg: (selector, data) => renderCarouselLG(selector, data.data),
        gcsm: (selector, data) => renderGridCardSM(selector, data.data),
        gcmd: (selector, data) => renderGridCardMD(selector, data.data, {
          BASE_WEB: window.AppConfig.BASE_WEB,
          user: data.member
        }),
        bbn:  (selector, data) => renderBanners(selector, data.data),
        intd: (selector, data) => renderIntroduce(selector, data.data)
      };

      // โหลดข้อมูลทุก section พร้อมกัน
      const results = await Promise.all(
        sections.data.map(async (section) => {
          try {
            const data = await fetchIndexData(section.req, service);
            return { section, data };
          } catch (err) {
            console.error(`Fetch failed for section ${section.type}`, err);
            return { section, data: null };
          }
        })
      );

      // render ตามลำดับ sections
      for (const { section, data } of results) {
        if (!data) continue;
        const renderer = renderMap[section.type];
        if (renderer) {
          try {
            renderer(`#${section.carouselId}`, data);
          } catch (err) {
            console.error(`Render failed for section ${section.type}`, err);
          }
        }
      }

    })
    .catch((e) => console.error("Module import failed", e));
</script>




</body>

</html>