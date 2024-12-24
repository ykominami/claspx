
RSpec.describe Claspx::Env do
  it '環境変数GOOGLE_API_KEY表示' do 
    expect( Claspx::Util.is_nil_or_whitespace(Claspx::Env::GOOGLE_API_KEY) ).to be_falsey
  end
end
