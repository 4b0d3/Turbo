import * as THREE from '../build/three.module.js'

export class CharacterControls {    
    // temporary data
    walkDirection = new THREE.Vector3();
    rotateAngle = new THREE.Vector3(0, 1, 0);
    rotateQuaternion = new THREE.Quaternion();
    cameraTarget = new THREE.Vector3();
    
    // constants
    fadeDuration = 0.2;
    runVelocity = 1000;
    walkVelocity = 500;

    constructor(model,mixer, animationsMap, orbitControl, camera, currentAction) {
        this.model = model;
        this.mixer = mixer;
        this.animationsMap = animationsMap;
        this.currentAction = currentAction;
        this.orbitControl = orbitControl;
        this.camera = camera;
        this.updateCameraTarget(0,0)
    }

    update(delta, keysPressed) {
        const directionPressed = ["z","q","s","d"].some(key => keysPressed[key] == true);

        let play = "";
        if (directionPressed && keysPressed["shift"]) {
            play = 'Run';
        } else if (directionPressed) {
            play = 'Walk';
        } else {
            play = 'Idle';
        }

        if (this.currentAction != play) {
            this.currentAction = play;
        }

        this.mixer.update(delta); // Met à jour l'animation avec la nouvelle valeur delta

        if (this.currentAction == 'Run' || this.currentAction == 'Walk') {
            // calculate towards camera direction
            let angleYCameraDirection = Math.atan2(
                    (this.camera.position.x - this.model.position.x), 
                    (this.camera.position.z - this.model.position.z));
            // diagonal movement angle offset
            let directionOffset = this.directionOffset(keysPressed);

            // rotate model
            this.rotateQuaternion.setFromAxisAngle(this.rotateAngle, angleYCameraDirection + 4.1);
            this.model.quaternion.rotateTowards(this.rotateQuaternion, 0.2);

            // calculate direction
            this.camera.getWorldDirection(this.walkDirection);
            this.walkDirection.y = 0;
            this.walkDirection.normalize();
            this.walkDirection.applyAxisAngle(this.rotateAngle, directionOffset); // Appliquer une rotation

            // run/walk velocity
            const velocity = this.currentAction == 'Run' ? this.runVelocity : this.walkVelocity;

            // move model & camera
            const moveX = this.walkDirection.x * velocity * delta;
            const moveZ = this.walkDirection.z * velocity * delta;
            this.model.position.x += moveX;
            this.model.position.z += moveZ;
            this.updateCameraTarget(moveX, moveZ);
        }
    }

    updateCameraTarget(moveX, moveZ) {
        // move camera
        this.camera.position.x += moveX;
        this.camera.position.z += moveZ;

        // update camera target
        this.cameraTarget.x = this.model.position.x;
        this.cameraTarget.y = this.model.position.y + 230;
        this.cameraTarget.z = this.model.position.z;
        this.orbitControl.target = this.cameraTarget; // On définit le point de centrage de l'obitcontrol la ou se situe le model 3D
    }

    directionOffset(keysPressed) {
        let directionOffset = 0; // z

        if (keysPressed["z"]) {
            if (keysPressed["q"]) {
                directionOffset = Math.PI / 4; // z+q
            } else if (keysPressed["d"]) {
                directionOffset = - Math.PI / 4; // z+d
            }
        } else if (keysPressed["s"]) {
            if (keysPressed["q"]) {
                directionOffset = Math.PI / 4 + Math.PI / 2; // s+q
            } else if (keysPressed["d"]) {
                directionOffset = -Math.PI / 4 - Math.PI / 2; // s+d
            } else {
                directionOffset = Math.PI; // s
            }
        } else if (keysPressed["q"]) {
            directionOffset = Math.PI / 2; // q
        } else if (keysPressed["d"]) {
            directionOffset = - Math.PI / 2; // d
        }

        return directionOffset;
    }
}