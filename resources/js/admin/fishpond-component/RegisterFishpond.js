import input from './Input';
/**
 * Componente encargado de controlar el registro 
 * y la actalización de los estanques de peces.
 */
const RegisterFishpond = {
  /** Es el objeto con la información del estanque original */
  originalData: null,
  /** Es el titulo que se muestra en el formulario */
  title: 'Registrar Estanque',
  /** Contiene las claves de los campos del formulario */
  inputs: {},
  /** Nombre del estanque */
  name: null,
  /** Tipo de estanque */
  type: null,
  /** diameter del estanque circular */
  diameter: null,
  /** Ancho del estanque rectangular */
  width: null,
  /** Longitud del estanque rectangular */
  long: null,
  /** Altura maxima del estanque */
  maxHeight: null,
  /** Altura efectiva del estanque */
  effectiveHeight: null,
  /** Define si la dubla max y efectiva son correcta */
  errorInDepth: false,
  /** Capacidad de peces que puede soportar */
  capacity: null,
  /** Se encarga de hacer las peticiones al servidor */
  wire: undefined,
  /** Encargado de emitir los eventos customizados */
  dispatch: null,
  /** Establece si el componente está esperando la respuesta del servidor */
  waiting: false,
  /** Establece el estado de formulario de reistro */
  register: true,
  /** Establece el estado de formulario de actualización */
  updating: false,
  // *===============================================*
  // *========== Metodos de Inicialización ==========*
  // *===============================================*
  /**
   * Se encarga de inicializar todos los requerimientos del component
   * @param {*} wire Función de livewire
   * @param {*} dispatch Administrador de customevent de alpine
   */
  init(wire, dispatch) {
    this.wire = wire;
    this.dispatch = dispatch;
    this.__buildInputs();
  },
  /**
   * Este metodo es el encargado de crear los campos
   * del formulario de registro
   */
  __buildInputs() {


    this.name = input({
      id: 'fishpondName',
      name: 'name',
      label: 'Nombre',
      placeholder: 'Escribe el nombre del estanque',
      required: true,
    });

    this.type = input({
      id: 'fishpondType',
      name: 'type',
      label: 'Tipo de estanque',
      value: 'rectangular',
      required: true,
    });

    this.diameter = input({
      id: 'fishponddiameter',
      name: 'diameter',
      label: 'Diametro del Estanque <span class="text-xs">[m]<span>',
      placeholder: 'Ingresa el diametro del estanque',
      type: 'number',
      min: 0.01,
      max: 999.99,
      step: 0.01,
    });

    this.capacity = input({
      id: 'fishpondCapacity',
      name: 'capacity',
      label: 'Capacidad <span class="text-xs">[und]<span>',
      type: 'number',
      placeholder: 'Ingresa la capacidad del estanque',
      min: 1,
      max: 65535,
      step: 1,
    });

    this.width = input({
      id: 'fishpondWidth',
      name: 'width',
      label: 'Ancho <span class="text-xs">[m]<span>',
      type: 'number',
      placeholder: 'ej: 200.45',
      min: 0.01,
      max: 999.99,
      step: 0.01,
    });

    this.long = input({
      id: 'fishpondLong',
      name: 'long',
      label: 'Largo <span class="text-xs">[m]<span>',
      type: 'number',
      placeholder: 'ej: 23.4',
      min: 0.01,
      max: 999.99,
      step: 0.01,
    });

    this.maxHeight = input({
      id: 'fishpondMaxHeight',
      name: 'maxHeight',
      label: 'Maxima <span class="text-xs">[m]<span>',
      placeholder: 'ej: 3.2',
      type: 'number',
      min: 0.01,
      max: 9.99,
      step: 0.01,
    });

    this.effectiveHeight = input({
      id: 'fishpondEffectiveHeight',
      name: 'effectiveHeight',
      label: 'Efectiva <span class="text-xs">[m]<span>',
      placeholder: 'ej: 3',
      type: 'number',
      min: 0.01,
      max: 9.99,
      step: 0.01,
    });

    this.inputs.name = this.name;
    this.inputs.capacity = this.capacity;
    this.inputs.type = this.type;
    this.inputs.diameter = this.diameter;
    this.inputs.width = this.width;
    this.inputs.long = this.long;
    this.inputs.maxHeight = this.maxHeight;
    this.inputs.effectiveHeight = this.effectiveHeight;

  },
  submit() {
    if (this.register) {
      this.storeFishpond();
    } else if (this.updating) {
      this.updateFishpond();
    }
  },
  storeFishpond() {
    if (this.validateRegister()) {
      this.disabledInputs();
      this.waiting = true;
      let data = this.__buildData();
      const promise = this.wire.storeFishpond(data);
      promise.then(res => {
        if (res.isOk) {
          this.hidden();
          this.enabledInputs();
          this.dispatch('new-fishpond-registered', res.data)
        } else {
          this.notifyErrors(res.errors);
        }
        this.waiting = false;
      });
    }
  },
  updateFishpond() {
    if (this.validateRegister()) {
      this.disabledInputs();
      this.waiting = true;
      let data = this.__buildData();
      const promise = this.wire.updateFishpond(this.originalData.id, data);
      promise.then(res => {
        if (res.isOk) {
          this.hidden();
          this.dispatch('fishpond-updated', res.data)
        } else {
          this.notifyErrors(res.errors);
        }
        this.waiting = false;
        this.enabledInputs();
      });
    }
  },
  /**
   * Se encarga de montar los datos del estanque a los campos del formulario
   * y de actualizar el formulario para que sea de actualización.
   * @param {*} data Instancia de estanque
   */
  mountFishpond(data) {
    //Se resetean los campos
    this.resetInputs();
    //Se cargan los valores
    this.originalData = data;
    this.name.value = data.name;
    this.capacity.value = data.capacity;
    this.diameter.value = data.diameter
    this.width.value = data.width;
    this.long.value = data.long;
    this.maxHeight.value = data.maxHeight;
    this.effectiveHeight.value = data.effectiveHeight;
    this.type.value = data.type;
    //Se habilita el formulario de actualización
    this.enabledUpdatingForm();
  },
  // *================================================*
  // *================== Utilidades ==================*
  // *================================================*
  /**
   * Resetea los campos del formulario y lo restaura 
   * a la forma de registro para luego emitir un evento
   * que notifica al componente principal de ocultar el modal
   */
  hidden() {
    this.resetInputs();
    this.enabledRegisterForm();
    this.errorInDepth = false;
    this.dispatch('cancel-register');
  },
  /**
   * Se encarga de resetear el valores de cada uno de los 
   * campos del formulario y de eleiminar los posibles errores
   */
  resetInputs() {
    for (const name in this.inputs) {
      if (Object.hasOwnProperty.call(this.inputs, name)) {
        const input = this.inputs[name];
        input.reset();
      }
    }

    //Se resetea la alerta de profundidad
    this.errorInDepth = false;

    //Se regresa el tipo al valor por defecto
    this.type.value = 'rectangular';
  },
  /**
   * Se encarga de modificar los parametros del componente 
   * para habilitar el formulario a registro
   */
  enabledRegisterForm() {
    this.register = true;
    this.updating = false;
    this.title = "Registrar Estanque";
  },
  /**
   * Se encarga de modificar los parametros del componente 
   * para habilitar el formulario de actualización
   */
  enabledUpdatingForm() {
    this.register = false;
    this.updating = true;
    this.title = "Actualizar Registro";
  },
  /**
   * Se encarga de deshabilitar todos los campos del formulario
   * con el fin de que no sean editados mientras se hace una petición
   */
  disabledInputs() {
    for (const name in this.inputs) {
      if (Object.hasOwnProperty.call(this.inputs, name)) {
        const input = this.inputs[name];
        input.disabled = true;
      }
    }
  },
  /**
   * Se encarga de habilitar los campos del formulario
   * una vez que la petición del servidor sea completada
   */
  enabledInputs() {
    for (const name in this.inputs) {
      if (Object.hasOwnProperty.call(this.inputs, name)) {
        const input = this.inputs[name];
        input.disabled = false;
      }
    }
  },
  /**
   * Se encarga de crear la estructura de datos que el servidor utilizará 
   * para crear el nuevo registro en la base de datos
   * @returns {object}
   */
  __buildData() {
    let name = this.name.value;
    let capacity = this.capacity.value ? parseInt(this.capacity.value) : null;
    let type = this.type.value;
    let diameter = null;
    let width = null;
    let long = null;
    let max_height = this.maxHeight.value ? parseFloat(this.maxHeight.value): null;
    let effective_height = this.effectiveHeight.value ? parseFloat(this.effectiveHeight.value) : null;

    //Se establecen los parametros de diseño segun el tipo de estanque
    if(type === 'circular'){
      diameter = this.diameter.value ? parseFloat(this.diameter.value) : null;
    }else if( type === 'rectangular'){
      width = this.width.value ? parseFloat(this.width.value) : null;
      long = this.long.value ? parseFloat(this.long.value) : null;
    }


    return {name, capacity, type, diameter, width, long, max_height, effective_height};
  },
  // *================================================*
  // *================= Validaciones =================*
  // *================================================*
  /**
   * Este metodo se encarga de validar el campo del formulario
   * @param {string} name Clave de identificación del campo a vaidar
   */
  validateInput(key) {
    //Se recupera el objeto que constrola el campo
    let input = this.inputs[key];

    //Se valida si el campo es requerido
    if (key === 'name') {
      this.validateName();
    } else if (key === 'type') {
      this.validateType();
    } else {
      this.validateNumber(input);
    }

    //Reglas adicionales para gestionar la profundidad
    if (key === 'maxHeight' || key === 'effectiveHeight') {
      this.validateDepth();
    }
  },
  /**
   * Contiene las reglas de validación para el campo nombre
   * @param {input} input Objeto con los parametros del campo
   */
  validateName() {
    let value = this.name.value;
    if (value && value.length > 0) {
      if (value.length < 3) {
        this.name.setError("Nombre demasiado corto");
      } else if (value.length > 20) {
        this.name.setError('El nombre es demasiado largo');
      } else {
        this.name.isOk();
      }
    } else {
      this.name.setError('Este campo es requerido.');
    }
  }, //end method
  validateType() {
    let value = this.type.value;
    if (value === 'circular' || value === 'rectangular') {
      this.type.isOk();
    } else {
      this.type.setError('Tipo de estanque inválido');
    }
  },
  /**
   * Este metodo se encarga de verificar que el valor del campo
   * sea un numero valido y ademas controla las validaciones del mismo
   * @param {*} input Objeto con la información del campo a calidar
   */
  validateNumber(input) {
    let value = input.value;

    /**
     * Como esstos campos no son obligatorios la validación solo se hace
     * unicamente cuando el campo tiene algun valor
     */
    if (value) {
      value = parseFloat(input.value);
      //Se verifica que es un numero válido
      if (!isNaN(value)) {
        if (typeof input.min !== 'undefined' && typeof input.max !== 'undefined') {
          if (value >= input.min) {
            if (value <= input.max) {
              input.isOk();
            } else {
              input.setError(`Debe ser menor o igual que ${input.max} m`);
            }
          } else {
            input.setError(`Debe ser mayor o igual que ${input.min}  m`);
          }
        } else {
          if (typeof input.min !== 'undefined') {
            if (value >= input.min) {
              input.isOk();
            } else {
              input.setError(`Debe ser mayor o igual que ${input.min}  m`);
            }
          } else if (typeof input.max !== 'undefined') {
            if (value <= input.max) {
              input.isOk();
            } else {
              input.setError(`Debe ser menor o igual que ${input.max} m`);
            }
          } else {
            input.isOk();
          }
        }

      } else {
        input.setError(`valor inválido`);
      }
    } else {
      input.isOk();
    }

  },
  /**
   * Este metodo es el encargado de verificar que los campos
   * introducidos por el usuario son correctos y darle paso al
   * a que el componente realice la peticion
   * @returns {boolean}
   */
  validateRegister() {
    let isOk = true;
    for (const name in this.inputs) {
      if (Object.hasOwnProperty.call(this.inputs, name)) {
        const input = this.inputs[name];
        this.validateInput(name);
        if (input.hasError) {
          isOk = false;
        }
      }
    }

    this.validateDepth();

    return isOk && !this.errorInDepth;
  },
  /**
   * Se encarga de verificar que los campos de la profundiad sean correctos
   * verificando que la profundiad maxima sea mayor o igual que la efectiva
   */
  validateDepth() {
    let max = this.maxHeight;
    let effective = this.effectiveHeight;
    let maxValue = parseFloat(max.value);
    let effectiveValue = parseFloat(effective.value);

    if (!isNaN(maxValue) && !isNaN(effectiveValue)) {
      if(maxValue >= effectiveValue){

        /**
         * Solo se eliminan las alertas si el campon tiene un error
         * y el mensaje que tienen es un campo vacio
         */

        if(max.hasError && max.errorMessage.length <= 0){
          max.isOk();
        }

        if(effective.hasError && effective.errorMessage.length <= 0){
          effective.isOk();
        }

        this.errorInDepth = false;
      }else{

        /**
         * Solo se agregan las alertas vaciías si el campo
         * no tienen errores de ningun tipo.
         */
        if(!max.hasError){
          max.setError('');
        }

        if(!effective.hasError){
          effective.setError('');
        }
        this.errorInDepth = true;
      }
    }else{
      if(max.hasError && max.errorMessage.length <= 0){
        max.isOk();
      }

      if(effective.hasError && effective.errorMessage.length <= 0){
        effective.isOk();
      }

      this.errorInDepth = false;
    }
  },
  /**
   * Se encarga de notificar a la vista los erroes provenientes del
   * servidor
   */
  notifyErrors(errors) {
    for (const key in errors) {
      if (Object.hasOwnProperty.call(errors, key)) {
        const error = errors[key];
        if (Object.hasOwnProperty.call(this.inputs, key)) {
          this.inputs[key].setError(error);
        }
      }
    }
  }

}

export default RegisterFishpond;