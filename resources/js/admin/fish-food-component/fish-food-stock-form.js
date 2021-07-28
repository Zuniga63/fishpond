import input from '../input';

window.fishFoodStockForm = () => {
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
    fishFoodList: [],
    fishFoodId: null,
    quantity: null,
    amount: null,
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
      this.__createInputs();

      //Se crea el listado de alimentos
      window.initialData.fishFoodList.forEach(item => {
        this.fishFoodList.push({
          id: item.id,
          name: item.name
        });
      });
    },
    __createInputs() {
      // CANTIDAD DEL STOCK A INGRESAR
      this.quantity = input({
        id: 'stockQuantity',
        name: 'quantity',
        label: 'Cantidad [Kg]',
        placeholder: 'Escribe los Kg a ingresar.',
        require: true,
        min: 1,
        max: 16700,
        step: 0.001
      });

      this.fishFoodId = input({
        id: 'stockFishFoodId',
        name: 'fishFoodId',
        label: 'Alimento',
        placeholder: 'Selecciona el alimento a ingresar.',
        require: true,
        min: 1,
      });

      //IMPORTE
      this.amount = input({
        id: 'expenseAmount',
        name: 'amount',
        label: 'Importe',
        placeholder: '$0 [COP]',
        require: true,
        min: 100,
        max: 100000000
      });
    },
    // *=====================================================*
    // *================ METODOS DE UTILIDAD ================*
    // *=====================================================*
    reset() {
      this.visible = false;
      this.mode = "register";
      this.waiting = false;

      this.quantity.reset();
      this.amount.reset();
      this.fishFoodId.reset();
    },
    /**
     * Este metodo establece los parametros basicos con lo que
     * se inicializa el formulario cuando es llamado por un evento global.
     * @param {*} detail Detalles del modo en el que se habilita el formulario
     */
    enableForm(detail) {
      this.visible = true;
      this.mode = detail.mode;
      this.fishFoodId.value = detail.fishFoodId;

      // if (detail.mode === 'updating') {
      //   //TODO
      // }
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
    formatAmount() {
      //Recupero el valor
      let value = window.deleteCurrencyFormat(this.refs.amount.value);
      this.amount.value = value;
      //Se vuelve a formatear
      this.refs.amount.value = window.formatCurrency(value, 0);
    },
    /**
     * Carga en el listado los alimentos que son creados por
     * un formulario.
     * @param {*} data datos de una instancia de alimento
     */
    addFishFood(data){
      //Se crea la instancia
      this.fishFoodList.push({
        id: data.id,
        name: data.name
      })
    },
    /**
     * Se encarga de actualizar el nombre de un alimento actualizado
     * por otro componente.
     * @param {*} data Datos de una instancia de alimento
     */
    updateFishFood(data){
      //Se recupera la original
      let original = this.fishFoodList.find(item => item.id === data.id);
      //Se actualiza
      original.name = data.name;
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
      this.wire.storeFishFoodStock(data)
        .then(res => {
          if (res.ok) {
            this.dispatch('stock-was-stored', {
              fishFoodId: data.fishFoodId,
              stock: res.stock
            });
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
      //TODO
    },
    getSubmitData() {
      let data = {
        fishFoodId: this.fishFoodId.value,
        quantity: this.quantity.value,
        amount: this.amount.value
      }

      return data;
    },
    // *===============================================*
    // *================ VALIDACIONES =================*
    // *===============================================*
    validateAmount() {
      let value = this.amount.value;
      let min = this.amount.min;
      let max = this.amount.max;
      let errorMessage = null;

      if (value) {
        if (value >= min) {
          if (value < max) {
            this.amount.isOk();
            return true;
          } else {
            errorMessage = `El importe debe ser menor que ` + window.formatCurrency(max, 0);
          }
        } else {
          errorMessage = "El importe minimo debe ser de " + window.formatCurrency(min, 0);
        }
      } else {
        errorMessage = "Este campo es obligatorio."
      }

      this.amount.setError(errorMessage);
      return false;
    },
    validateQuantity() {
      let value = this.quantity.value;
      let error = null;

      if (value) {
        if (value >= this.quantity.min) {
          if (value < this.quantity.max) {
            this.quantity.isOk();
            return true;
          } else {
            error = `Debe ser menor que ${window.formatCurrency(this.quantity.max, 0, 'decimal')} Kg.`
          }
        } else {
          error = `Debe ser minimo de ${window.formatCurrency(this.quantity.min, 0, 'decimal')} Kg.`
        }
      } else {
        error = 'Este campo es requerido';
      }

      this.quantity.setError(error);
      return false;
    },
    validateFishFoodId(){
      let value = this.fishFoodId.value;
      let error = null;

      if(value){
        if(this.fishFoodList.some(item => item.id === value)){
          this.fishFoodId.isOk();
          return true;
        }else{
          error = 'El alimento no existe.';
        }
      }else{
        error = "Se debe seleccionar uno.";
      }

      this.fishFoodId.setError(error);
      return false;
    },
    /**
     * Realiza todas las validaciones del formulario
     * y returna true si todas fueron correctas.
     * @returns bool
     */
    validateSubmit() {
      let validations = [];
      validations.push(this.validateAmount());
      validations.push(this.validateQuantity());
      validations.push(this.validateFishFoodId());

      //Retorna false si alguna de las validaciones es falsa, pero valida todos los campos
      return !validations.some(val => val === false);
    }
  }
}