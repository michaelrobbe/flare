import React from 'react';
import {Modal, Button} from 'react-bootstrap';
import ItemDetails from './item-details';

export default class PurchaseModal extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      item: null,
      loading: true,
    }
  }

  componentDidMount() {
    axios.get('/api/market-board/' + this.props.modalData.item_id + '/listing-details')
      .then((result) => {
        this.setState({
          item: result.data,
          loading: false,
        })
      }).catch((error) => {
        if (error.hasOwnProperty('response')) {
          const response = error.response;

          if (response.status === 401) {
            return location.reload();
          }

          if (response.status === 429) {
            return window.location = '/game';
          }
        }
      });
  }

  hasEnoughGold() {
    return (this.props.characterGold - (this.props.modalData.listed_price * 1.05)) > 0;
  }

  belongsToCharacter() {
    return parseInt(this.props.characterId) === this.props.modalData.character_id;
  }

  purchase() {
    axios.post('/api/market-board/purchase/' + this.props.characterId, {
      market_board_id: this.props.modalData.id
    }).then((result) => {
      this.props.updateMessage('You purchased the ' + this.props.modalData.name + ' for: ' + (this.props.modalData.listed_price * 1.05) + ' Gold.', 'success');
      this.props.closeModal();
    }).catch((err) => {
      if (err.hasOwnProperty('response')) {
        const response = err.response;

        if (response.status === 401) {
          location.reload();
        }

        if (response.status === 429) {
          return window.location = '/game';
        }
      }
    });
  }

  render() {
    return (
      <Modal
        show={this.props.showModal}
        onHide={this.props.closeModal}
        backdrop="static"
        keyboard={false}
        dialogClassName="large-modal "
        size="lg"
      >
        <Modal.Header closeButton>
          <Modal.Title>{this.props.modalData.name}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          {
            !this.hasEnoughGold() && !this.belongsToCharacter() ?
              <div className="alert alert-danger mb-2 mt-2">You do not have enough gold to buy this.</div> : null
          }

          {
            this.belongsToCharacter() ?
              <div className="alert alert-danger mb-2 mt-2">You cannot purchase your own item.
                If you would like to delist this item, head over to your My listings section under the market to delist.</div> : null
          }
          <p>Is this the item you would like to purchase? It
            will <strong>cost</strong>: {this.props.modalData.listed_price * 1.05} Gold (incl. 5% tax)</p>
          {this.state.loading ? 'Loading please wait ...' : <ItemDetails item={this.state.item}/>}
        </Modal.Body>
        <Modal.Footer>
          <Button variant="danger" onClick={this.props.closeModal}>
            Close
          </Button>
          <Button
            variant="primary"
            onClick={this.purchase.bind(this)}
            disabled={!this.hasEnoughGold() || this.belongsToCharacter()}
          >
            Purchase
          </Button>
        </Modal.Footer>
      </Modal>
    );
  }
}
