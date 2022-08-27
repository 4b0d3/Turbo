import * as THREE from '../build/three.module.js';
import { OrbitControls } from './jsm/controls/OrbitControls.js';
import { GLTFLoader } from './jsm/loaders/GLTFLoader.js';
import { CharacterControls } from './characterControls.js';
import Stats from './jsm/libs/stats.module.js';
import * as creation from './Fonctions.js';       // Appel de notre fichier fonctions.js
import { GUI }  from './jsm/libs/dat.gui.module.js';        // Appel du fichier pour intégrer un GUI

/****** PARAMETRES ******/
let RESOURCES_LOADED = true; // Permet de savoir si toutes les ressources ont été chargée, pour enlever la page de chargement
let SCREEN_LOADER = true; // Si true affiche une page de chargement
let FPV_MODE = true;

/****** CONSTANTES ******/
const pi = Math.PI;
const twoPi = Math.PI * 2;
const manager = creation.manager;
const loader = new GLTFLoader(manager);

/****** VARIABLES ******/
let loadingScreen, loadingBar;
let cameras = creation.getCameras();
let camera, scene, renderer, controls;
camera = cameras["main"];

let x, y, z;
let pivot; // Pont levis
let ambient, spotLight, spotLight2, spotLight3, spotLight4, dot_light, sphere;
let lightHelper, shadowCameraHelper;
let elements = {};
let characterControls, characterOrbitControls, keysPressed = {};
let stats, mixer, clock = new THREE.Clock();
let phoenixGroup = new THREE.Group();


/****** SCREEN LOADER ******/
if (SCREEN_LOADER) {
  RESOURCES_LOADED = false;

  let loaderPercentage = document.getElementById("loading");

  loadingScreen = {
      scene: new THREE.Scene(),
      camera: new THREE.PerspectiveCamera( 70, window.innerWidth/window.innerHeight, 1, 100 )
  };

  loadingScreen.camera.lookAt(0,0,0);
  loadingScreen.camera.position.z = 20;

  loadingBar = new THREE.Mesh(new THREE.RingGeometry( 9, 10, 150, 30, pi, 0 ), new THREE.MeshBasicMaterial({color: "#ffffff", side: THREE.DoubleSide}))
  loadingBar.rotation.y = pi;
  loadingScreen.scene.add(loadingBar);
  loaderPercentage.classList = "";
  loaderPercentage.classList.add("show");
  loaderPercentage.textContent = "0%";

  /****** MANAGER ACTIONS ******/
  manager.onStart = function ( url, itemsLoaded, itemsTotal ) {
      console.log( 'Started loading file: ' + url + '.\nLoaded ' + itemsLoaded + ' of ' + itemsTotal + ' files.' );
  };

  manager.onLoad = function ( ) {
      RESOURCES_LOADED = true;
      loaderPercentage.classList = "";
      loaderPercentage.classList.add("hide");
      // console.log( 'Loading complete!');
  };

  manager.onProgress = function ( url, itemsLoaded, itemsTotal ) {
      let percentage = itemsLoaded/itemsTotal;
      loaderPercentage.textContent = percentage.toFixed(2)*100 + "%";
      loadingBar.geometry = new THREE.RingGeometry( 9, 10, 50, 30, pi*0.5, twoPi * percentage);
      // console.log( 'Loading file: ' + url + '.\nLoaded ' + itemsLoaded + ' of ' + itemsTotal + ' files.' );
  };

  manager.onError = function ( url ) {
      console.log( 'There was an error loading ' + url );
  };
}


init();           // Fonction d'initialisation pour créer les objets
animate();        // Fonction qui permet de faire les animations


function init() {
  /****** SCENE ******/
  scene = new THREE.Scene();

  /****** RENDERER ******/
  renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setClearColor( 0x87CEEB, 1);
  // enable les ombres et le type de l'ombre
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  renderer.outputEncoding = THREE.sRGBEncoding;
  document.body.appendChild(renderer.domElement);

  /****** EVENT LISTENNERS ******/
  window.addEventListener('resize', onWindowResize); // When window's resized
  document.addEventListener("keypress", (event) => {
    if(event.key.toLowerCase() == "m") {
        FPV_MODE = !FPV_MODE;
    }
  })

  document.addEventListener("keydown", (event) => {
      keysPressed[event.key.toLowerCase()] = true;
  })

  document.addEventListener("keyup", (event) => {
      keysPressed[event.key.toLowerCase()] = false;
  })

  /****** STATS ******/
  stats = new Stats();
  document.body.appendChild(stats.dom);

  /****** CONTROLS ******/
  // Main
  controls = new OrbitControls(camera, renderer.domElement); // define methode of controls
  controls.maxPolarAngle = Math.PI / 2;
  controls.minPolarAngle = Math.PI / 4;
  controls.maxDistance = 2000; //limit camera zoom outward
  controls.minDistance = 100; //limit camera zoom inward
  controls.enablePan = false; //Stop camera panning
  controls.update();

  // Character
  characterOrbitControls = new OrbitControls(cameras["character"], renderer.domElement);
  characterOrbitControls.minDistance = 30;
  characterOrbitControls.maxDistance = 30;
  characterOrbitControls.enablePan = false;
  characterOrbitControls.maxPolarAngle = Math.PI - pi/4;
  characterOrbitControls.update();

  

  /****** FUNCTIONS CALLS ******/
  lights();
  // solarSystem();
  loadCharacter();


  loader.load(
    // Ressource URL
    HOST + "javascript/game/examples/3Delements/Mountain/city.glb",
    // Called when the ressource is loaded
    function ( gltf ) {
      const model = gltf.scene;
      model.position.set(-4000,-45,-15000);
      let elementMesh = gltf.scene.children[0];
      // elementMesh.scale.set(10000, 10000, 10000);

      scene.add(model);
    }
  );
}

function onWindowResize() {

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);

}

function animate() {

  requestAnimationFrame(animate);

  if(!(RESOURCES_LOADED)) {
    renderer.render(loadingScreen.scene, loadingScreen.camera);
    return; // Stop the function here.
  }

  let mixerUpdateDelta = clock.getDelta();
  if(characterControls && FPV_MODE) {
      characterControls.update(mixerUpdateDelta, keysPressed);
  }

  stats.update();
  // mixer.update(mixerUpdateDelta);
  renderer.render(scene, FPV_MODE ? cameras["character"] : camera);
}

function lights() {
  /***** LIGHTS *****/
  // création d'une lumière ambiante
  ambient = new THREE.AmbientLight( 0xffffff, 1 );
  scene.add( ambient );
}

function loadCharacter() {
  loader.load (
    // Ressource URL
    HOST + "javascript/game/examples/3Delements/scooterr/source/scene.glb",
    // Called when the ressource is loaded
    function ( gltf ) {
        const model = gltf.scene;
        model.traverse(function (object) { if(object.isMesh) object.castShadow = true;})
        scene.add(model);
        model.position.set(0,-193,0);
        model.children[0].scale.set(12, 12, 12);

        const gltfAnimations = gltf.animations;
        const mixer = new THREE.AnimationMixer(model); // Définition du mixer object qui permet de jouer les animations
        const animationsMap = new Map(); // Création d'un dictionnaire qui contiendra les animations du personnage
        gltfAnimations.filter(a => a.name != 'TPose').forEach(a => {
            animationsMap.set(a.name, mixer.clipAction(a)); // ajouter les animations instance de AnimationAction au dictionnaires
        });

        characterControls = new CharacterControls(model, mixer, animationsMap, characterOrbitControls, cameras["character"], "Idle");
    }
  );
}




