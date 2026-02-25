import {useContext} from 'react';
import {Routes as ReactRoutes, Route} from 'react-router-dom';
import {AppContext} from '../../context/AppProvider';
import { routes as AppRoutes} from "../../routes/routes"

const Routes  = () => {
    const { user } = useContext(AppContext);
    return (
            user != null ?
                <ReactRoutes>
                    {AppRoutes.map((route,i) => {
                        return (
                            <Route exact path={route.path} key={i} element={<route.page.component />}></Route>
                        )})}
                </ReactRoutes>
                :null
    );
}

export default Routes;
