import { isEmpty } from 'lodash';
import input from '../input';

window.fishFoodForm = () => {
  return {
    /** Define si el formulario se muestra o no */
    visible: false,
    /** 
     * Establece el tipo de formulario, 
     * si register para nuevo costo o 
     * updating para actualizar uno existente 
     * */
    mode: 'register',
    /**
     * Instnacia del lote de peces en el que se desea 
     * crear o actualizar un costo.
     */
    // *===========================================*
    // *========== Campos del formulario ==========*
    // *===========================================*
    fishFood: null,
    name: null,
    brand: null,
    stages: null,
    stage: null,
    // *====================================================*
    // *========== Relacionadas con el componente ==========*
    // *====================================================*
    /** Define si el componente está esperando una respuesta del servidor */
    waiting: false,
    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *=====================================================*
    // *============= METODOS DE INICIALIZACIÓN =============*
    // *=====================================================*
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
      this.stages = window.initialData.stages;
      this.__createInputs();
    },
    __createInputs() {
      //NOMBRE
      this.name = input({
        id: 'fishFoodName',
        name: 'name',
        label: 'Nombre',
        placeholder: 'Escribe el nombre aquí.',
        min: 3,
        max: 50,
      });
      //MARCA
      this.brand = input({
        id: 'fishFoodBrand',
        name: 'brand',
        label: 'Marca',
        placeholder: 'Escribe la marca aquí.',
        min: 3,
        max: 50,
      });
      //STAGE
      this.stage = input({
        id: 'fishFoodStage',
        name: 'stage',
        label: 'Etapa',
        placeholder: 'Selecciona una etapa',
      });
    },
    // *=====================================================*
    // *================ METODOS DE UTILIDAD ================*
    // *=====================================================*
    reset() {
      this.visible = false;
      this.mode = "register";
      this.waiting = false;

      //Se resetean los campos
      this.fishFood = null;
      this.name.reset();
      this.brand.reset();
      this.stage.reset();
    },
    /**
     * Este metodo establece los parametros basicos con lo que
     * se inicializa el formulario cuando es llamado por un evento global.
     * @param {*} detail Detalles del modo en el que se habilita el formulario
     */
    enableForm(detail) {
      this.visible = true;
      this.mode = detail.mode;

      if (detail.mode === 'updating') {
        this.fishFood = detail.fishFood;
        this.name.value = detail.fishFood.name;
        this.brand.value = detail.fishFood.brand;
        this.stage.value = detail.fishFood.stage;
      }
    },
    cancel() {
      this.reset();
      this.dispatch('cancel-form-operation');
    },
    notifyErrors(errors) {
      //Esto se encarga de actualizar los campos del formulario
      for (const key in errors) {
        if (Object.hasOwnProperty.call(errors, key)) {
          const error = errors[key];
          if (Object.hasOwnProperty.call(this, key)) {
            this[key].setError(error);
          }
        }
      }
    },
    // *=======================================*
    // *================ FETCH ================*
    // *=======================================*
    submit() {
      if (this.validateSubmit() && !this.waiting) {
        if (this.mode === 'register') {
          this.__store();
        } else if (this.mode === 'updating') {
          this.__update();
        }
      }
    },
    __store() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.storeFishFood(data)
        .then(res => {
          if (res.ok) {
            this.dispatch('fish-food-was-stored', res.fishFood);
            setTimeout(() => {
              this.reset();
            }, 200);
          } else {
            this.notifyErrors(res.errors);
            console.log(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
    },
    __update() {
      this.waiting = true;
      let data = this.getSubmitData();
      this.wire.updateFishFood(data)
        .then(res => {
          if (res.ok) {
            this.dispatch('fish-food-was-updated', res.fishFood);
            setTimeout(() => {
              this.reset();
            }, 200);
          } else {
            this.notifyErrors(res.errors);
            console.log(res.errors);
          }
        }).catch(error => {
          console.log(error);
        }).finally(() => {
          this.waiting = false;
        })
      //TODO
    },
    getSubmitData() {
      let data = {
        name: this.name.value.trim(),
        brand: this.brand.value.trim(),
        stage: this.stage.value
      };

      if (this.mode === 'updating') {
        data.fishFoodId = this.fishFood.id;
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateName() {
      let value = typeof this.name.value === 'string'
        ? this.name.value.trim()
        : this.name.value;
      let error = null;

      if (!isEmpty(value)) {
        if (value.length >= this.name.min) {
          if (value.length <= this.name.max) {
            this.name.isOk();
            return true;
          } else {
            error = `Debe tener máximo ${this.name.max} caracteres.`;
          }
        } else {
          error = `Debe tener minimo ${this.name.min} caracteres.`
        }
      } else {
        error = 'Este campo es obligatorio.'
      }

      this.name.setError(error);
      return false;
    },
    validateBrand() {
      let value = typeof this.brand.value === 'string'
        ? this.brand.value.trim()
        : this.brand.value;
      let error = null;

      if (!isEmpty(value)) {
        if (value.length >= this.brand.min) {
          if (value.length <= this.brand.max) {
            this.brand.isOk();
            return true;
          } else {
            error = `Debe tener máximo ${this.brand.max} caracteres.`;
          }
        } else {
          error = `Debe tener minimo ${this.brand.min} caracteres.`
        }
      } else {
        error = 'Este campo es obligatorio.'
      }

      this.brand.setError(error);
      return false;
    },
    validateStage() {
      let value = this.stage.value;
      let error = null;
      if (value) {
        if (Object.hasOwnProperty.call(this.stages, value)) {
          this.stage.isOk();
          return true;
        } else {
          error = 'Esta opción no existe';
        }
      } else {
        error = 'Se debe seleccionar una etapa';
      }

      this.stage.setError(error);
      return false;
    },
    /**
     * Realiza todas las validaciones del formulario
     * y returna true si todas fueron correctas.
     * @returns bool
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateName());
      validations.push(this.validateBrand());
      validations.push(this.validateStage());

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }
}