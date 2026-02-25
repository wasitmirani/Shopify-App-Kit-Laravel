import './bootstrap';
import App from './components/App'
import ReactDOM from 'react-dom'

const rootElement = document.getElementById("app");
if(rootElement){
    ReactDOM.render(<App />, rootElement)
}
