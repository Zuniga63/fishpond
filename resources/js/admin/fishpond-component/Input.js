/**
 * Contiene el objeto que controla el formulario
 * @param {int} id Identificador para la etiqueta y el input
 * @param {string} name Nombre del campo en el componente
 * @param {string} label Es el valor que se imprime en la etiqueta
 * @returns Objeto para el componente alpine
 */
const input = (config) => {
  return {
    id: config.id,
    name: config.name,
    label: config.label,
    placeholder: config.placeholder,
    type: config.type ? config.type : 'text',
    min: config.min,
    max: config.max,
    step: config.step,
    required: config.required,
    value: config.value ? config.value : null,
    default: config.value ? config.value : null,
    hasError: false,
    errorMessage: null,
    disabled: false,
    /**
     * Regresa el formulario a su 
     * estado inicial
     */
    reset() {
      this.value = this.default;
      this.hasError = false;
      this.errorMessage = null;
    },
    /**
     * Actualiza el estado del formulario para notificar que existe un error
     * @param {string} message Mensaje a mostra en la interfaz
     */
    setError(message) {
      this.hasError = true;
      this.errorMessage = message;
    },
    /**
     * Limpia los erroes que este pueda tener
     */
    isOk() {
      this.hasError = false;
      this.errorMessage = null;
    }
  }
}

export default input;